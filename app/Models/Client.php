<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address_1',
        'address_2'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
