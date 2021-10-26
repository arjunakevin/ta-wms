<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use mikehaertl\wkhtmlto\Pdf;
use App\Models\DeliveryOrder;
use App\Models\OutboundDelivery;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\DeliveryOrderFormRequest;
use App\Http\Resources\AppDeliveryOrderResponse;
use App\Http\Requests\DeliveryOrderCheckFormRequest;
use App\Exceptions\DeliveryOrderOutboundDeliveryException;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DeliveryOrder::with('outbound_delivery.client')
            ->latest()
            ->paginate();

        return inertia()->render('DeliveryOrder/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\OutboundDelivery  $outbound
     * @return \Illuminate\Http\Response
     */
    public function create(OutboundDelivery $outbound)
    {
        $details = $outbound->open_details()
            ->with('product')
            ->get();

        $outbound->load('client');

        return inertia()->render('DeliveryOrder/Form', compact('outbound', 'details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DeliveryOrderFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryOrderFormRequest $request)
    {
        $do = DB::transaction(function () use ($request) {
            return tap(
                DeliveryOrder::create($request->validated()),
                function ($do) {
                    foreach ($do->outbound_delivery->open_details as $detail) {
                        $do->createDetail($detail);
                    }

                    $do->outbound_delivery->updateStatus();
                }
            );
        });

        return redirect()->route('delivery_orders.show', $do);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryOrder $delivery_order)
    {
        $delivery_order->load('outbound_delivery.client', 'inventories');

        $details = $delivery_order->details()
            ->with('outbound_delivery_detail.product', 'inventory')
            ->get();

        return inertia()->render('DeliveryOrder/Detail', compact('delivery_order', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryOrder $delivery_order)
    {
        $outbound = $delivery_order->outbound_delivery->load('client');
        $details = $delivery_order->details()
            ->with('outbound_delivery_detail.product')
            ->get();

        return inertia()->render('DeliveryOrder/Form', compact('delivery_order', 'outbound', 'details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\DeliveryOrderFormRequest  $request
     * @param  \App\Models\DeliveryOrder  $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryOrderFormRequest $request, DeliveryOrder $delivery_order)
    {
        $delivery_order->update($request->validated());

        return redirect()->route('delivery_orders.show', $delivery_order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryOrder  $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryOrder $delivery_order)
    {
        if ($delivery_order->isProcessed()) {
            throw ValidationException::withMessages([
                'message' => 'Delivery order is already processed.'
            ]);
        }

        DB::transaction(function () use ($delivery_order) {
            foreach ($delivery_order->details as $detail) {
                $detail->restoreOutboundDeliveryDetailOpenQuantity();
            }
    
            $delivery_order->delete();

            $delivery_order->outbound_delivery->updateStatus();
        });

        return redirect()->route('delivery_orders.index');
    }

    /**
     * Search outbound delivery.
     *
     * @param Request  $request
     * @return void
     */
    public function searchOutbound(Request $request)
    {
        try {
            $outbound = OutboundDelivery::whereReference($request->reference)
                ->whereHas('client', function ($q) use ($request) {
                    $q->whereCode($request->client_code);
                })->first();
    
            if (!$outbound) {
                throw new DeliveryOrderOutboundDeliveryException('Outbound delivery data not found.');
            } else if ($outbound->status == OutboundDelivery::STATUS_FULLY_COMMITTED) {
                throw new DeliveryOrderOutboundDeliveryException('Outbound delivery is fully received.');
            }

            return redirect()->route('delivery_orders.create', $outbound);
        } catch (DeliveryOrderOutboundDeliveryException $e) {
            throw ValidationException::withMessages([
                'outbound' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delivery order check index.
     *
     * @return \Illuminate\Http\Response
     */
    public function check(DeliveryOrder $delivery_order)
    {
        $details = $delivery_order->details()
            ->with('outbound_delivery_detail.product')
            ->get();

        $delivery_order->load('outbound_delivery');

        $pick_completed = $delivery_order->inventories()->sum('base_quantity') == $delivery_order->details()->sum('base_quantity');

        return inertia()->render('DeliveryOrder/Check', compact('delivery_order', 'details', 'pick_completed'));
    }

    /**
     * Submit good receive check.
     *
     * @param \App\Http\Requests\DeliveryOrderCheckFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function submitCheck(DeliveryOrderCheckFormRequest $request, DeliveryOrder $delivery_order)
    {
        $request->detail->decrement('open_check_quantity', $request->base_quantity);

        $delivery_order->updateCheckStatus();

        if ($request->wantsJson()) {
            return $this->appDeliveryOrderCheckData($delivery_order);
        }

        return redirect()->route('delivery_orders.check', $delivery_order);
    }

    /**
     * Remove stock from inventory
     *
     * @param DeliveryOrder $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function goodIssue(DeliveryOrder $delivery_order)
    {
        if ($delivery_order->status == DeliveryOrder::STATUS_GOOD_ISSUED) {
            throw ValidationException::withMessages([
                'message' => 'Delivery order already good issued.'
            ]);
        } else if ($delivery_order->status != DeliveryOrder::STATUS_FULLY_CHECKED) {
            throw ValidationException::withMessages([
                'message' => 'Delivery order is not fully checked.'
            ]);
        }

        DB::transaction(function () use ($delivery_order) {
            $delivery_order->goodIssue();
        });

        session()->flash('message', 'Good issue success.');

        return redirect()->route('delivery_orders.show', $delivery_order);
    }

    /**
     * Print delivery order report
     *
     * @return \Illuminate\Http\Response
     */
    public function report(DeliveryOrder $delivery_order)
    {
        $delivery_order->load('details.outbound_delivery_detail.product');

        $data = [
            'id' => $delivery_order->id,
            'name' => 'Good Issue Report',
            'qr' => (QrCode::size(85)->margin(0)->generate($delivery_order->id)->toHtml()),
            'header' => [
                1 => [
                    'title' => 'Reference',
                    'value' => $delivery_order->reference
                ],
                3 => [
                    'title' => 'Client Code',
                    'value' => $delivery_order->outbound_delivery->client->code
                ],
                5 => [
                    'title' => 'Issue Date',
                    'value' => $delivery_order->good_issue_date
                ],
                7 => [
                    'title' => 'Notes',
                    'value' => $delivery_order->notes
                ]
            ],
            'data' => $delivery_order->toArray(),
            'details' => $delivery_order->details->map(function ($detail) {
                return [
                    'product_code' => $detail->outbound_delivery_detail->product->code,
                    'description' => $detail->outbound_delivery_detail->product->description_1,
                    'base_quantity' => $detail->good_issue_quantity,
                    'uom_name' => $detail->outbound_delivery_detail->product->uom_name,
                ];
            })
        ];

        $data['qr'] = '<svg' . (Str::between($data['qr'], '<svg', 'svg>')) . 'svg>';

        $pdf = new Pdf([
            'binary' => 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf'
        ]);

        $pdf->addPage(view('reports.document', compact('data'))->render());

        return $pdf->send();
    }

    /**
     * Check item check status
     *
     * @param DeliveryOrder $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function appDeliveryOrderCheckSearch(DeliveryOrder $delivery_order)
    {
        $valid_statuses = [
            DeliveryOrder::STATUS_FULL_PICK,
            DeliveryOrder::STATUS_PARTIALLY_CHECKED
        ];

        if (!in_array($delivery_order->status, $valid_statuses)) {
            return response()->json([
                'message' => 'Delivery order ' . $delivery_order->id . ' has no check outstanding.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Ok.'
        ], Response::HTTP_OK);
    }

    /**
     * Get good receive check data
     *
     * @param DeliveryOrder $delivery_order
     * @return \Illuminate\Http\Response
     */
    public function appDeliveryOrderCheckData(DeliveryOrder $delivery_order)
    {
        $delivery_order->load('outbound_delivery.client', 'details.outbound_delivery_detail.product');

        return new AppDeliveryOrderResponse($delivery_order);
    }
}
