<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodReceiveDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_detail_id',
        'base_quantity',
        'receive_quantity',
        'open_check_quantity'
    ];

    protected $appends = [
        'check_quantity'
    ];

    public function inbound_delivery_detail()
    {
        return $this->belongsTo(InboundDeliveryDetail::class, 'inbound_detail_id');
    }

    public function getCheckQuantityAttribute()
    {
        return $this->base_quantity - $this->open_check_quantity;
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'detail_id');
    }
    
    public function restoreInboundDeliveryDetailOpenQuantity()
    {
        $this->inbound_delivery_detail->increment('open_quantity', $this->base_quantity);
    }
}
