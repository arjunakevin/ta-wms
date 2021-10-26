<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use mikehaertl\wkhtmlto\Pdf;
use App\Models\InboundDelivery;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\GoodReceiveFormRequest;
use App\Http\Resources\AppGoodReceiveResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\GoodReceiveCheckFormRequest;
use App\Exceptions\GoodReceiveInboundDeliveryException;

class GoodReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = GoodReceive::with('inbound_delivery.client')
            ->paginate();

        return inertia()->render('GoodReceive/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\InboundDelivery  $inbound
     * @return \Illuminate\Http\Response
     */
    public function create(InboundDelivery $inbound)
    {
        $details = $inbound->open_details()
            ->with('product')
            ->get();

        $inbound->load('client');

        return inertia()->render('GoodReceive/Form', compact('inbound', 'details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\GoodReceiveFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GoodReceiveFormRequest $request)
    {
        $gr = DB::transaction(function () use ($request) {
            return tap(
                GoodReceive::create($request->validated()),
                function ($gr) {
                    foreach ($gr->inbound_delivery->open_details as $detail) {
                        $gr->createDetail($detail);
                    }

                    $gr->inbound_delivery->updateStatus();
                }
            );
        });

        return redirect()->route('good_receives.show', $gr);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function show(GoodReceive $good_receive)
    {
        $good_receive->load('inbound_delivery.client', 'inventories');

        $details = $good_receive->details()
            ->with('inbound_delivery_detail.product', 'inventory')
            ->get();

        return inertia()->render('GoodReceive/Detail', compact('good_receive', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function edit(GoodReceive $good_receive)
    {
        $inbound = $good_receive->inbound_delivery->load('client');
        $details = $good_receive->details()
            ->with('inbound_delivery_detail.product')
            ->get();

        return inertia()->render('GoodReceive/Form', compact('good_receive', 'inbound', 'details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\GoodReceiveFormRequest  $request
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function update(GoodReceiveFormRequest $request, GoodReceive $good_receive)
    {
        $good_receive->update($request->validated());

        return redirect()->route('good_receives.show', $good_receive);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoodReceive $good_receive)
    {
        if ($good_receive->isProcessed()) {
            throw ValidationException::withMessages([
                'message' => 'Good receive is already processed.'
            ]);
        }

        DB::transaction(function () use ($good_receive) {
            foreach ($good_receive->details as $detail) {
                $detail->restoreInboundDeliveryDetailOpenQuantity();
            }
    
            $good_receive->delete();

            $good_receive->inbound_delivery->updateStatus();
        });

        return redirect()->route('good_receives.index');
    }

    /**
     * Search inbound delivery.
     *
     * @param Request  $request
     * @return void
     */
    public function searchInbound(Request $request)
    {
        try {
            $inbound = InboundDelivery::whereReference($request->reference)
                ->whereHas('client', function ($q) use ($request) {
                    $q->whereCode($request->client_code);
                })->first();
    
            if (!$inbound) {
                throw new GoodReceiveInboundDeliveryException('Inbound delivery data not found.');
            } else if ($inbound->status == InboundDelivery::STATUS_FULLY_RECEIVED) {
                throw new GoodReceiveInboundDeliveryException('Inbound delivery is fully received.');
            }

            return redirect()->route('good_receives.create', $inbound);
        } catch (GoodReceiveInboundDeliveryException $e) {
            throw ValidationException::withMessages([
                'inbound' => $e->getMessage()
            ]);
        }
    }

    /**
     * Good receive check index.
     *
     * @return \Illuminate\Http\Response
     */
    public function check(GoodReceive $good_receive)
    {
        $details = $good_receive->details()
            ->with('inbound_delivery_detail.product')
            ->paginate();

        $good_receive->load('inbound_delivery');
        
        return inertia()->render('GoodReceive/Check', compact('good_receive', 'details'));
    }

    /**
     * Submit good receive check.
     *
     * @param \App\Http\Requests\GoodReceiveCheckFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function submitCheck(GoodReceiveCheckFormRequest $request, GoodReceive $good_receive)
    {
        $request->detail->decrement('open_check_quantity', $request->base_quantity);

        $good_receive->updateCheckStatus();

        if ($request->wantsJson()) {
            return route('app.inbound_check.get', $good_receive);
        }

        return redirect()->route('good_receives.check', $good_receive);
    }

    /**
     * Add stock to inventory
     *
     * @param GoodReceive $good_receive
     * @return \Illuminate\Http\Response
     */
    public function receive(GoodReceive $good_receive)
    {
        if ($good_receive->status == GoodReceive::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'message' => 'Good receive is not checked.'
            ]);
        } else if ($good_receive->status >= GoodReceive::STATUS_RECEIVED || $good_receive->status == GoodReceive::STATUS_FULL_PUTAWAY) {
            throw ValidationException::withMessages([
                'message' => 'Good receive is already received.'
            ]);
        }

        DB::transaction(function () use ($good_receive) {
            $good_receive->receive();
        });

        session()->flash('message', 'Receive success.');

        return redirect()->route('good_receives.show', $good_receive);
    }

    /**
     * Print good receive report
     *
     * @return \Illuminate\Http\Response
     */
    public function print(GoodReceive $good_receive)
    {
        $good_receive->load('details.inbound_delivery_detail.product');

        $data = [
            'id' => $good_receive->id,
            'name' => 'Good Receive Report',
            'qr' => (QrCode::size(85)->margin(0)->generate($good_receive->id)->toHtml()),
            'header' => [
                1 => [
                    'title' => 'Reference',
                    'value' => $good_receive->reference
                ],
                3 => [
                    'title' => 'Client Code',
                    'value' => $good_receive->inbound_delivery->client->code
                ],
                5 => [
                    'title' => 'Receive Date',
                    'value' => $good_receive->receive_date
                ],
                7 => [
                    'title' => 'Notes',
                    'value' => $good_receive->notes
                ]
            ],
            'data' => $good_receive->toArray(),
            'details' => $good_receive->details->map(function ($detail) {
                return [
                    'product_code' => $detail->inbound_delivery_detail->product->code,
                    'description' => $detail->inbound_delivery_detail->product->description_1,
                    'base_quantity' => $detail->receive_quantity,
                    'uom_name' => $detail->inbound_delivery_detail->product->uom_name,
                ];
            })
        ];

        $data['qr'] = '<svg' . (Str::between($data['qr'], '<svg', 'svg>')) . 'svg>';

        $pdf = new Pdf([
            'binary' => 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf'
        ]);

        $pdf->addPage(view('prints.document', compact('data'))->render());

        return $pdf->send();
    }

    public function appGoodReceiveCheckSearch(GoodReceive $good_receive)
    {
        $valid_statuses = [
            GoodReceive::STATUS_DRAFT,
            GoodReceive::STATUS_PARTIALLY_CHECKED
        ];

        if (!in_array($good_receive->status, $valid_statuses)) {
            return response()->json([
                'message' => 'Good receive ' . $good_receive->id . ' is fully checked.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Ok.'
        ], Response::HTTP_OK);
    }

    /**
     * Search good receive check
     *
     * @param GoodReceive $good_receive
     * @return \Illuminate\Http\Response
     */
    public function appGoodReceiveCheckData(GoodReceive $good_receive)
    {
        $good_receive->load('inbound_delivery.client', 'details.inbound_delivery_detail.product');

        return new AppGoodReceiveResponse($good_receive);
    }

    /**
     * Submit good receive check
     *
     * @param GoodReceive $good_receive
     * @return \Illuminate\Http\Response
     */
    public function appGoodReceiveSubmitCheck(GoodReceiveCheckFormRequest $request, GoodReceive $good_receive)
    {   
        if ($good_receive->details->sum('open_check_quantity') <= 0) {
            return response()->json([
                'message' => 'Good receive ' . $good_receive->id . ' has no outstanding product to check.'
            ], 400);
        }

        $good_receive->load('inbound_delivery.client', 'details.inbound_delivery_detail.product');

        return new AppGoodReceiveResponse($good_receive);
    }
}
