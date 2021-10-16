<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InboundDelivery extends Model
{
    use HasFactory;

    const STATUS_UNRECEIVED = 1;
    const STATUS_PARTIALLY_RECEIVED = 2;
    const STATUS_FULLY_RECEIVED = -99;

    protected $fillable = [
        'reference',
        'client_id',
        'arrival_date',
        'po_date',
        'notes',
        'status'
    ];
    
    protected $appends = [
        'arrival_date_formatted',
        'po_date_formatted'
    ];

    /**
     * Update inbound delivery status
     *
     * @return void
     */
    public function updateStatus()
    {
        $base_quantity = $this->details->sum('base_quantity');
        $open_quantity = $this->details->sum('open_quantity');

        if ($open_quantity == 0) {
            $this->update([
                'status' => InboundDelivery::STATUS_FULLY_RECEIVED
            ]);
        } else if ($open_quantity < $base_quantity) {
            $this->update([
                'status' => InboundDelivery::STATUS_PARTIALLY_RECEIVED
            ]);
        }
    }

    public function processed()
    {
        return $this->status != InboundDelivery::STATUS_UNRECEIVED;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function details()
    {
        return $this->hasMany(InboundDeliveryDetail::class);
    }

    public function open_details()
    {
        return $this->details()->open();
    }

    public function getArrivalDateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        return Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function getArrivalDateFormattedAttribute()
    {
        $original = $this->getRawOriginal('arrival_date');

        if (!$original) {
            return null;
        }

        $date = Carbon::parse($original);

        return $date->format('Y-m-d') . 'T' . $date->format('H:i');
    }

    public function getPoDateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        return Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function getPoDateFormattedAttribute()
    {
        $original = $this->getRawOriginal('po_date');

        if (!$original) {
            return null;
        }

        $date = Carbon::parse($original);

        return $date->format('Y-m-d') . 'T' . $date->format('H:i');
    }
}
