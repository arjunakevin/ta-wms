<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'barcode',
        'client_id',
        'description_1',
        'description_2',
        'uom_name',
        'is_active'
    ];
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
