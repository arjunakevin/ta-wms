<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodReceive extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 1;
    const STATUS_PARTIALLY_CHECKED = 2;
    const STATUS_FULLY_CHECKED = 3;

    protected $fillable = [
        'inbound_delivery_id',
        'reference',
        'receive_date',
        'notes',
        'status'
    ];

    protected $appends = [
        'receive_date_formatted'
    ];

    public function createDetail(InboundDeliveryDetail $detail)
    {
        $this->details()->create([
            'inbound_detail_id' => $detail->id,
            'base_quantity' => $detail->open_quantity,
            'receive_quantity' => 0,
            'open_check_quantity' => $detail->open_quantity,
            'open_putaway_quantity' => 0
        ]);

        $detail->update([
            'open_quantity' => 0
        ]);
    }

    public function updateCheckStatus()
    {
        $base_quantity = $this->details->sum('base_quantity');
        $open_check_quantity = $this->details->sum('open_check_quantity');

        if ($open_check_quantity == 0) {
            $this->update([
                'status' => GoodReceive::STATUS_FULLY_CHECKED
            ]);
        } else if ($open_check_quantity < $base_quantity) {
            $this->update([
                'status' => GoodReceive::STATUS_PARTIALLY_CHECKED
            ]);
        }
    }

    public function processed()
    {
        return $this->status > GoodReceive::STATUS_DRAFT;
    }

    public function inbound_delivery()
    {
        return $this->belongsTo(InboundDelivery::class);
    }

    public function details()
    {
        return $this->hasMany(GoodReceiveDetail::class);
    }

    public function getReceiveDateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        return Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function getReceiveDateFormattedAttribute()
    {
        $original = $this->getRawOriginal('receive_date');

        if (!$original) {
            return null;
        }

        $date = Carbon::parse($original);

        return $date->format('Y-m-d') . 'T' . $date->format('H:i');
    }
}
