<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboundDeliveryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'outbound_delivery_id',
        'line_id',
        'product_id',
        'base_quantity',
        'open_quantity'
    ];

    public function outbound_delivery()
    {
        return $this->belongsTo(OutboundDelivery::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('open_quantity', '>=', 1);
    }

    public function isCommitted()
    {
        return $this->base_quantity != $this->open_quantity;
    }
}
