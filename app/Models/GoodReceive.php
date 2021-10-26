<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodReceive extends Document
{
    use HasFactory;

    const STATUS_DRAFT = 1;
    const STATUS_PARTIALLY_CHECKED = 2;
    const STATUS_FULLY_CHECKED = 3;
    const STATUS_RECEIVED = 4;
    const STATUS_PARTIAL_PUTAWAY = 5;
    const STATUS_FULL_PUTAWAY = -1;

    const STATUS_LABEL = [
        GoodReceive::STATUS_DRAFT => 'Draft',
        GoodReceive::STATUS_PARTIALLY_CHECKED => 'Partially Checked',
        GoodReceive::STATUS_FULLY_CHECKED => 'Fully Checked',
        GoodReceive::STATUS_RECEIVED => 'Received',
        GoodReceive::STATUS_PARTIAL_PUTAWAY => 'Partial Putaway',
        GoodReceive::STATUS_FULL_PUTAWAY => 'Full Putaway'
    ];

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
            'open_check_quantity' => $detail->open_quantity
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

    public function isProcessed()
    {
        return $this->status != GoodReceive::STATUS_DRAFT;
    }

    public function inbound_delivery()
    {
        return $this->belongsTo(InboundDelivery::class);
    }

    public function details()
    {
        return $this->hasMany(GoodReceiveDetail::class);
    }

    public function inventories()
    {
        return $this->morphMany(Inventory::class, 'documentable');
    }

    public function putaway_list()
    {
        return $this->inventories()->where('base_quantity', '>', 0);
    }
    
    public function movement_orders()
    {
        return $this->morphMany(MovementOrder::class, 'documentable');
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

    /**
     * Receive and add stock
     *
     * @return void
     */
    public function receive()
    {
        if (!$this->relationLoaded('details.inbound_delivery_detail')) {
            $this->load('details.inbound_delivery_detail');
        }
    
        foreach ($this->details as $detail) {
            if ($detail->check_quantity < $detail->base_quantity) {
                $detail->inbound_delivery_detail->increment('open_quantity', $detail->open_check_quantity);
            }

            $this->inventories()->create([
                'detail_id' => $detail->id,
                'product_id' => $detail->inbound_delivery_detail->product_id,
                'base_quantity' => $detail->check_quantity,
                'posting_date' => $this->getRawOriginal('receive_date')
            ]);

            $detail->update([
                'receive_quantity' => $detail->check_quantity
            ]);
        }

        $this->updateReceiveStatus();

        $this->updateReceiveStatus();

        $this->inbound_delivery->updateStatus();
    }

    /**
     * Update receive status
     *
     * @return void
     */
    public function updateReceiveStatus()
    {
        $this->update([
            'status' => GoodReceive::STATUS_RECEIVED
        ]);
    }

    public function updateMovementStatus()
    {
        $receive_quantity = $this->details->sum('receive_quantity');
        $inventory_base_quantity = $this->inventories->sum('base_quantity');
        $inventory_pick_quantity = $this->inventories->sum('pick_quantity');
        $total_quantity = $inventory_base_quantity + $inventory_pick_quantity;

        if ($total_quantity == 0) {
            $this->update([
                'status' => GoodReceive::STATUS_FULL_PUTAWAY
            ]);
        } else if ($total_quantity < $receive_quantity || $inventory_pick_quantity > 1) {
            $this->update([
                'status' => GoodReceive::STATUS_PARTIAL_PUTAWAY
            ]);
        }
    }

    public function getStatusLabel()
    {
        return GoodReceive::STATUS_LABEL[$this->status];
    }
}
