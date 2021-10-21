<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'source_inventory_id',
        'source_location_id',
        'destination_inventory_id',
        'destination_location_id',
        'base_quantity',
        'status'
    ];
    
    const STATUS_OPEN = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_CANCELED = -1;

    public function movement_order()
    {
        return $this->belongsTo(MovementOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function destination_location()
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function source_location()
    {
        return $this->belongsTo(Location::class, 'source_location_id');
    }

    public function source_inventory()
    {
        return $this->belongsTo(Inventory::class, 'source_inventory_id');
    }

    public function destination_inventory()
    {
        return $this->belongsTo(Inventory::class, 'destination_inventory_id');
    }

    /**
     * Confirm movement order
     *
     * @return void
     */
    public function confirm()
    {
        $this->resetInventoryPickPutQuantity();
        $this->source_inventory->decrement('base_quantity', $this->base_quantity);
        $this->destination_inventory->increment('base_quantity', $this->base_quantity);

        $this->update([
            'status' => MovementOrderDetail::STATUS_CONFIRMED
        ]);

        $this->movement_order->document->updateMovementStatus();

        if (!($this->source_inventory->base_quantity || $this->source_inventory->pick_quantity || $this->source_inventory->put_quantity)) {
            $this->source_inventory->delete();
        }
    }

    /**
     * Cancel movement order
     *
     * @return void
     */
    public function cancel()
    {
        $this->resetInventoryPickPutQuantity();

        $this->update([
            'status' => MovementOrderDetail::STATUS_CANCELED
        ]);
    }

    /**
     * Reset inventory pick and put quantity
     *
     * @return void
     */
    public function resetInventoryPickPutQuantity()
    {
        $this->source_inventory->decrement('pick_quantity', $this->base_quantity);
        $this->destination_inventory->decrement('put_quantity', $this->base_quantity);
    }
}
