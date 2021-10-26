<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'detail_id',
        'base_quantity',
        'pick_quantity',
        'put_quantity',
        'posting_date'
    ];

    protected $appends = [
        'available_pick_quantity'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('client_id', function ($query) {
            if (auth()->check() && auth()->user()->client_id != null) {
                $query->whereHas('product', function ($query) {
                    $query->where('client_id', auth()->user()->client_id);
                });
            }
        });
    }

    public function getAvailablePickQuantityAttribute()
    {
        return $this->base_quantity - $this->pick_quantity;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function createPutMovement(MovementOrder $movement_order, Location $location, int $base_quantity)
    {
        $destination_inventory = Inventory::create([
            'product_id' => $this->product_id,
            'posting_date' => $this->posting_date,
            'location_id' => $location->id,
            'base_quantity' => 0,
            'pick_quantity' => 0,
            'put_quantity' => $base_quantity
        ]);

        $detail = $movement_order->details()->create([
            'is_pick' => 0,
            'product_id' => $this->product_id,
            'source_inventory_id' => $this->id,
            'source_location_id' => $this->documentable_id,
            'destination_inventory_id' => $destination_inventory->id,
            'destination_location_id' => $destination_inventory->location_id,
            'base_quantity' => $base_quantity
        ]);

        $this->increment('pick_quantity', $base_quantity);

        return $detail;
    }

    public function createPickMovement(MovementOrder $movement_order, $detail, int $base_quantity)
    {
        $destination_inventory = $movement_order->documentable->inventories()->create([
            'product_id' => $this->product_id,
            'detail_id' => $detail->id,
            'posting_date' => $this->posting_date,
            'base_quantity' => 0,
            'pick_quantity' => 0,
            'put_quantity' => $base_quantity
        ]);

        $movement_order->details()->create([
            'is_pick' => 1,
            'product_id' => $this->product_id,
            'source_inventory_id' => $this->id,
            'source_location_id' => $this->location_id,
            'destination_inventory_id' => $destination_inventory->id,
            'destination_location_id' => $destination_inventory->documentable_id,
            'base_quantity' => $base_quantity
        ]);

        $this->increment('pick_quantity', $base_quantity);
    }
}
