<?php

namespace App\Models;

use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    protected static function booted() {
        static::addGlobalScope(new ClientScope);
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeFindByCodeOrBarcode($query, $code)
    {
        $query->whereCode($code)
            ->when(!empty($code), function ($query) use ($code) {
                $query->orWhere('barcode', $code);
            });
    }
}
