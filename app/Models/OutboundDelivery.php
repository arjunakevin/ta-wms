<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundDelivery extends Model
{
    use HasFactory;

    const STATUS_UNCOMMITTED = 1;

    protected $fillable = [
        'reference',
        'client_id',
        'destination_name',
        'destination_phone',
        'destination_address_1',
        'destination_address_2',
        'request_delivery_date',
        'po_reference',
        'notes',
        'status'
    ];

    protected $appends = [
        'request_delivery_date_formatted'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function details()
    {
        return $this->hasMany(OutboundDeliveryDetail::class);
    }

    public function getRequestDeliveryDateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        return Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function getRequestDeliveryDateFormattedAttribute()
    {
        $original = $this->getRawOriginal('request_delivery_date');

        if (!$original) {
            return null;
        }

        $date = Carbon::parse($original);

        return $date->format('Y-m-d') . 'T' . $date->format('H:i');
    }

    /**
     * Update outbound delivery status
     *
     * @return void
     */
    public function updateStatus()
    {
        $base_quantity = $this->details->sum('base_quantity');
        $open_quantity = $this->details->sum('open_quantity');

        if ($open_quantity == 0) {
            $this->update([
                'status' => OutboundDelivery::STATUS_FULLY_COMMITTED
            ]);
        } else if ($open_quantity < $base_quantity) {
            $this->update([
                'status' => OutboundDelivery::STATUS_PARTIALLY_COMMITTED
            ]);
        } else {
            $this->update([
                'status' => OutboundDelivery::STATUS_UNCOMMITTED
            ]);
        }
    }

    public function isProcessed()
    {
        return $this->status != OutboundDelivery::STATUS_UNCOMMITTED;
    }
}
