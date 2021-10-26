<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryOrder extends Model
{
    use HasFactory;

    const STATUS_UNALLOCATED = 1;
    const STATUS_PARTIAL_PICK = 2;
    const STATUS_FULL_PICK = 3;
    const STATUS_PARTIALLY_CHECKED = 4;
    const STATUS_FULLY_CHECKED = 5;
    const STATUS_GOOD_ISSUED = -1;

    protected $fillable = [
        'outbound_delivery_id',
        'reference',
        'good_issue_date',
        'notes',
        'status'
    ];

    protected $appends = [
        'good_issue_date_formatted'
    ];

    public function outbound_delivery()
    {
        return $this->belongsTo(OutboundDelivery::class);
    }

    public function details()
    {
        return $this->hasMany(DeliveryOrderDetail::class);
    }

    public function inventories()
    {
        return $this->morphMany(Inventory::class, 'documentable');
    }

    public function movement_orders()
    {
        return $this->morphMany(MovementOrder::class, 'documentable');
    }

    public function isProcessed()
    {
        return $this->status != DeliveryOrder::STATUS_UNALLOCATED;
    }

    public function createDetail(OutboundDeliveryDetail $detail)
    {
        $this->details()->create([
            'outbound_detail_id' => $detail->id,
            'base_quantity' => $detail->open_quantity,
            'good_issue_quantity' => 0,
            'open_check_quantity' => $detail->open_quantity
        ]);

        $detail->update([
            'open_quantity' => 0
        ]);
    }

    public function getGoodIssueDateAttribute($data)
    {
        if (!$data) {
            return null;
        }

        return Carbon::parse($data)->format('d/m/Y H:i');
    }

    public function getGoodIssueDateFormattedAttribute()
    {
        $original = $this->getRawOriginal('good_issue_date');

        if (!$original) {
            return null;
        }

        $date = Carbon::parse($original);

        return $date->format('Y-m-d') . 'T' . $date->format('H:i');
    }

    public function updateCheckStatus()
    {
        $base_quantity = $this->details->sum('base_quantity');
        $open_check_quantity = $this->details->sum('open_check_quantity');

        if ($open_check_quantity == 0) {
            $this->update([
                'status' => DeliveryOrder::STATUS_FULLY_CHECKED
            ]);
        } else if ($open_check_quantity < $base_quantity) {
            $this->update([
                'status' => DeliveryOrder::STATUS_PARTIALLY_CHECKED
            ]);
        }
    }

    /**
     * Issue and remove stock
     *
     * @return void
     */
    public function goodIssue()
    {    
        if (!$this->relationLoaded('details.inventory')) {
            $this->load('details.inventory');
        }
    
        foreach ($this->details as $detail) {
            $detail->update([
                'good_issue_quantity' => $detail->base_quantity
            ]);
        }

        $this->inventories()->delete();

        $this->update([
            'status' => DeliveryOrder::STATUS_GOOD_ISSUED
        ]);
    }

    public function isFullyPicked()
    {
        return false;
    }

    public function updateMovementStatus()
    {
        $base_quantity = $this->details->sum('base_quantity');
        $open_pick_quantity = $this->details->sum('open_pick_quantity');

        if ($open_pick_quantity == 0) {
            $this->update([
                'status' => DeliveryOrder::STATUS_FULL_PICK
            ]);
        } else if ($open_pick_quantity < $base_quantity || $open_pick_quantity > 1) {
            $this->update([
                'status' => DeliveryOrder::STATUS_PARTIAL_PICK
            ]);
        }
    }
}