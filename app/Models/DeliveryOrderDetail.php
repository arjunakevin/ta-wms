<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'outbound_detail_id',
        'base_quantity',
        'good_issue_quantity',
        'open_check_quantity'
    ];

    protected $appends = [
        'check_quantity',
        'open_pick_quantity'
    ];

    public function outbound_delivery_detail()
    {
        return $this->belongsTo(OutboundDeliveryDetail::class, 'outbound_detail_id');
    }

    public function getCheckQuantityAttribute()
    {
        return $this->base_quantity - $this->open_check_quantity;
    }

    public function delivery_order()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'detail_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'detail_id');
    }
    
    public function restoreOutboundDeliveryDetailOpenQuantity()
    {
        $this->outbound_delivery_detail->increment('open_quantity', $this->base_quantity);
    }

    public function getOpenPickQuantityAttribute()
    {
        if ($this->delivery_order->status == DeliveryOrder::STATUS_GOOD_ISSUED) {
            return 0;
        }

        return $this->base_quantity - ($this->inventories->sum('base_quantity') + $this->inventories->sum('put_quantity'));
    }
}
