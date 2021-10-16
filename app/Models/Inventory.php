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
        'base_quantity',
        'pick_quantity',
        'put_quantity',
        'posting_date'
    ];
}
