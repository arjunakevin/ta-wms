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

    public function getAvailablePickQuantityAttribute()
    {
        return $this->base_quantity - $this->pick_quantity;
    }
}
