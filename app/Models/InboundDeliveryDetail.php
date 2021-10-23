<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundDeliveryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_delivery_id',
        'line_id',
        'product_id',
        'base_quantity',
        'open_quantity'
    ];

    public function inbound_delivery()
    {
        return $this->belongsTo(InboundDelivery::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('open_quantity', '>=', 1);
    }

    public function isReceived()
    {
        return $this->base_quantity != $this->open_quantity;
    }
}
