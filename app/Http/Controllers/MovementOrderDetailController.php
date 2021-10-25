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
        $putaway = $movement_order->isPutaway();

        if ($putaway) {
            $details = $movement_order->documentable->inventories()->with('product')->get();
        } else {
            $details = $movement_order->documentable->details()->with('outbound_delivery_detail.product')->get();
        }

        return inertia()->render('MovementOrder/Detail', compact('movement_order', 'details', 'putaway'));
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
            $base_quantity = $request->base_quantity;

            $request->inventories->each(function ($inventory) use ($request, $movement_order, $base_quantity) {
                if ($movement_order->isPutaway()) {
                    $inventory->createPutMovement(
                        $movement_order,
                        Location::findOrFail($request->location_id),
                        $request->base_quantity
                    );
                } else {
                    $pick_quantity = $inventory->available_pick_quantity >= $base_quantity
                        ? $base_quantity
                        : $inventory->available_pick_quantity;

                    $inventory->createPickMovement(
                        $movement_order,
                        $request->detail,
                        $pick_quantity
                    );

                    $base_quantity -= $pick_quantity;

                    if ($base_quantity <= 0) {
                        return false;
                    }
                }
            });

            $movement_order->documentable->updateMovementStatus();
        });

        return redirect()->route('movement_order_details.create', $movement_order);
    }
}
