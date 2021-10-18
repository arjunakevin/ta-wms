<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovementOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'date'
    ];

    protected $appends = [
        'date_formatted'
    ];

    const TYPE_PUTAWAY = 1;
    const TYPE_PICKING = 2;

    public function document()
    {
        return $this->morphTo('documentable');
    }

    public function details()
    {
        return $this->hasMany(MovementOrderDetail::class);
    }

    public function getDateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        return Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function getDateFormattedAttribute()
    {
        $original = $this->getRawOriginal('date');

        if (!$original) {
            return null;
        }

        $date = Carbon::parse($original);

        return $date->format('Y-m-d') . 'T' . $date->format('H:i');
    }
}
