<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\MovementOrder;
use Illuminate\Support\Facades\DB;
use App\Models\MovementOrderDetail;
use App\Http\Requests\PickFormRequest;
use App\Http\Resources\PicklistResponse;
use App\Exceptions\MovementDocumentException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MovementOrderFormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MovementOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = MovementOrderDetail::with(
                'movement_order',
                'source_location',
                'destination_location',
                'product'
            )
            ->latest()
            ->paginate();
        
        $data->getCollection()
        ->transform(function ($data) {
            if ($data->is_pick) {
                $data->location = $data->source_location;
            } else {
                $data->location = $data->destination_location;
            }

            $data->movement_order->date = $data->movement_order->date_formatted;

            return $data;
        });

        return inertia()->render('MovementOrder/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param  string  $type
     * @param  string  $document_id
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type, $document_id)
    {
        $document = null;
        $inventories = null;

        if ($type == MovementOrder::TYPE_PUTAWAY) {
            $document = GoodReceive::findOrFail($document_id);
            $details = $document->inventories()->with('product')->get();
        } else {
            $document = DeliveryOrder::findOrFail($document_id);
            $details = $document->details()->with('outbound_delivery_detail.product')->get();
        }

        return inertia()->render('MovementOrder/Form', compact('type', 'document', 'details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MovementOrderFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovementOrderFormRequest $request)
    {
        $movement_order = DB::transaction(function () use ($request) {
            $document = null;
    
            if ($request->type == MovementOrder::TYPE_PUTAWAY) {
                $document = GoodReceive::findOrFail($request->document_id);
            } else {
                $document = DeliveryOrder::findOrFail($request->document_id);
            }
    
            return $document->movement_orders()->create($request->validated());
        });

        return redirect()->route('movement_order_details.create', $movement_order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MovementOrder  $movement_order
     * @return \Illuminate\Http\Response
     */
    public function show(MovementOrder $movement_order)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MovementOrder  $movement_order
     * @return \Illuminate\Http\Response
     */
    public function edit(MovementOrder $movement_order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MovementOrderFormRequest  $request
     * @param  \App\Models\MovementOrder  $movement_order
     * @return \Illuminate\Http\Response
     */
    public function update(MovementOrderFormRequest $request, MovementOrder $movement_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MovementOrder  $movement_order
     * @return \Illuminate\Http\Response
     */
    public function destroy(MovementOrder $movement_order)
    {
        //
    }

    /**
     * Confirm movement order
     *
     * @param Request $request
     * @return void
     */
    public function confirm(Request $request)
    {
        $count = $this->process($request->id, 'confirm');

        session()->flash('message', "${count} movement order(s) confirmed successfully.");

        return redirect()->route('movement_orders.index');
    }
    
    /**
     * Cancel movement order
     *
     * @param Request $request
     * @return void
     */
    public function cancel(Request $request)
    {
        $count = $this->process($request->id, 'cancel');

        session()->flash('message', "${count} movement order(s) canceled successfully.");

        return redirect()->route('movement_orders.index');
    }

    /**
     * Display a listing of the resource (putaway / picking).
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $data = MovementOrder::withCount('details')
            ->with('documentable')
            ->latest()
            ->paginate();

        $data->getCollection()->transform(function ($data) {
            if ($data->documentable instanceof GoodReceive) {
                $data->type = 'Put';
            } else {
                $data->type = 'Pick';
            }

            return $data;
        });

        return inertia()->render('MovementOrder/List', compact('data'));
    }

    /**
     * Search document for movement order
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchDocument(Request $request)
    {
        $document = null;

        try {
            if ($request->type == MovementOrder::TYPE_PUTAWAY) {
                $gr = GoodReceive::find($request->id);

                if (!$gr) {
                    throw new MovementDocumentException('Good receive data not found.');
                } else if ($gr->putaway_list->isEmpty()) {
                    throw new MovementDocumentException('Good receive has no outstanding putaway.');
                }

                $document = $gr;
            } else {
                $do = DeliveryOrder::find($request->id);

                if (!$do) {
                    throw new MovementDocumentException('Delivery order data not found.');
                } else if ($do->isFullyPicked()) {
                    throw new MovementDocumentException('Delivery order has no outstanding pick.');
                }

                $document = $do;
            }
        } catch (MovementDocumentException $e) {
            throw ValidationException::withMessages([
                'document' => $e->getMessage()
            ]);
        }

        return redirect()->route('movement_orders.create', [
            'type' => $request->type,
            'document_id' => $document->id
        ]);
    }

    /**
     * Process movement order
     *
     * @param array  $id
     * @param string  $process
     * @return int
     */
    protected function process(array $id, string $process)
    {
        return DB::transaction(function () use ($id, $process) {
            $movements = MovementOrderDetail::whereIn('id', $id)
                ->whereStatus(MovementOrderDetail::STATUS_OPEN)
                ->get();

            $movements->each(function ($movement) use ($process) {
                $movement->$process();
            });

            return $movements->count();
        });
    }

    /**
     * Search movement order
     *
     * @param MovementOrder $movement_order
     * @return \Illuminate\Http\Response
     */
    public function appMovementSearch(MovementOrder $movement_order)
    {
        if ($movement_order->isPutaway()) {
            throw new ModelNotFoundException();
        } else if (!$movement_order->open_details()->exists()) {
            return response()->json([
                'message' => 'Picklist ' . $movement_order->id . ' has no picking outstanding.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Ok.'
        ], Response::HTTP_OK);
    }

    public function appPicklist(MovementOrder $movement_order)
    {
        $movement_order->load('documentable.outbound_delivery.client', 'open_details.product', 'open_details.source_location');

        return new PicklistResponse($movement_order);
    }

    /**
     * Submit picking
     *
     * @param \App\Reqyests\PickFormRequest $request
     * @param MovementOrder $movement_order
     * @return \Illuminate\Http\Response
     */
    public function appSubmitPick(PickFormRequest $request, MovementOrder $movement_order)
    {
        $movements = MovementOrderDetail::whereStatus(MovementOrderDetail::STATUS_OPEN)
            ->whereProductId($request->product_id)
            ->whereSourceLocationId($request->location_id)
            ->whereMovementOrderId($movement_order->id)
            ->get();

        $movement_quantity = $movements->sum('base_quantity');

        if ($movement_quantity <= 1) {
            throw ValidationException::withMessages([
                'base_quantity' => "Invalid product or location."
            ]);
        } else if ($movement_quantity != $request->base_quantity) {
            throw ValidationException::withMessages([
                'base_quantity' => "Pick quantity from this location should be ${movement_quantity}."
            ]);
        }
        
        $movements->each(function ($movement) {
            $movement->confirm();
        });

        return $this->appPicklist($movement_order);
    }
}
