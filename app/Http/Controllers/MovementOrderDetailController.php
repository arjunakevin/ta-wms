<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\MovementOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MovementOrderDetailFormRequest;

class MovementOrderDetailController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * 
     * @param MovementOrder  $movement_order
     * @return \Illuminate\Http\Response
     */
    public function create(MovementOrder $movement_order)
    {
        $inventories = $movement_order->documentable->inventories()->with('product')->get();

        return inertia()->render('MovementOrder/Detail', compact('movement_order', 'inventories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MovementOrderDetailFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovementOrderDetailFormRequest $request, MovementOrder $movement_order)
    {
        DB::transaction(function () use ($request, $movement_order) {
            $request->inventory->createPutMovement(
                $movement_order,
                Location::findOrFail($request->location_id),
                $request->base_quantity
            );

            $movement_order->documentable->updateMovementStatus();
        });

        return redirect()->route('movement_order_details.create', $movement_order);
    }
}
