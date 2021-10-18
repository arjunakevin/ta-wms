<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Http\Request;
use App\Models\MovementOrder;
use Illuminate\Support\Facades\DB;
use App\Models\MovementOrderDetail;
use App\Exceptions\MovementDocumentException;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\MovementOrderFormRequest;

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
            $inventories = $document->inventories()->with('product')->paginate();
        }

        return inertia()->render('MovementOrder/Form', compact('type', 'document', 'inventories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MovementOrderFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovementOrderFormRequest $request)
    {
        $document = null;

        if ($request->type == MovementOrder::TYPE_PUTAWAY) {
            $document = GoodReceive::findOrFail($request->document_id);
        }

        $document->movement_orders()->create($request->validated());

        return redirect()->route('movement_order_details.create');
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
     * Search document for movement order
     *
     * @param Request $request
     * @return void
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

            return $movements->count();;
        });
    }
}
