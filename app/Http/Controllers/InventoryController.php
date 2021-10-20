<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Inventory::with('product', 'location')
            ->select(
                'inventories.id',
                'inventories.location_id',
                'inventories.documentable_type',
                'inventories.documentable_id',
                'inventories.detail_id',
                'inventories.product_id',
                DB::raw('SUM(base_quantity) base_quantity'),
                DB::raw('SUM(put_quantity) put_quantity'),
                DB::raw('SUM(pick_quantity) pick_quantity')
            )
            ->groupBy(
                'product_id',
                'location_id',
                'documentable_id'
            )
            ->paginate();

        return inertia()->render('Inventory/Index', compact('data'));
    }
}
