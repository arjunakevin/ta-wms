<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\InboundDelivery;
use App\Models\OutboundDelivery;

class DashboardController extends Controller
{
    /**
     * Render dashboard page
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'inbound_data' => $this->getInboundData(),
            'gr_data' => $this->getGrData(),
            'outbound_data' => $this->getOutboundData(),
            'do_data' => $this->getDoData()
        ];

        return inertia()->render('Dashboard', compact('data'));
    }

    /**
     * Get dashboard data for mobile.
     */
    public function appDashboardData()
    {
        $data = [
            'inbound_count' => InboundDelivery::count(),
            'gr_count' => GoodReceive::count(),
            'outbound_count' => OutboundDelivery::count(),
            'do_count' => DeliveryOrder::count(),
        ];
        return response()->json($data);
    }
    
    protected function getInboundData()
    {
        $total = InboundDelivery::count();
        $unreceived = InboundDelivery::whereStatus(InboundDelivery::STATUS_UNRECEIVED)->count();
        $partial = InboundDelivery::whereStatus(InboundDelivery::STATUS_PARTIALLY_RECEIVED)->count();
        $full = InboundDelivery::whereStatus(InboundDelivery::STATUS_FULLY_RECEIVED)->count();
        
        return [
            [
                'label' => 'Unreceived',
                'count' => $unreceived,
                'percentage' => round($total ? ($unreceived / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Partially Received',
                'count' => $partial,
                'percentage' => round($total ? ($partial / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Fully Received',
                'count' => $full,
                'percentage' => round($total ? ($full / $total) * 100 : 0, 1)
            ]
        ];
    }

    protected function getGrData()
    {
        $total = GoodReceive::count();
        $draft = GoodReceive::whereStatus(GoodReceive::STATUS_DRAFT)->count();
        $partial_check = GoodReceive::whereStatus(GoodReceive::STATUS_PARTIALLY_CHECKED)->count();
        $full_check = GoodReceive::whereStatus(GoodReceive::STATUS_FULLY_CHECKED)->count();
        $received = GoodReceive::whereStatus(GoodReceive::STATUS_RECEIVED)->count();
        $partial_put = GoodReceive::whereStatus(GoodReceive::STATUS_PARTIAL_PUTAWAY)->count();
        $full_put = GoodReceive::whereStatus(GoodReceive::STATUS_FULL_PUTAWAY)->count();

        return [
            [
                'label' => 'Draft',
                'count' => $draft,
                'percentage' => round($total ? ($draft / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Partially Checked',
                'count' => $partial_check,
                'percentage' => round($total ? ($partial_check / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Fully Checked',
                'count' => $full_check,
                'percentage' => round($total ? ($full_check / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Received',
                'count' => $received,
                'percentage' => round($total ? ($received / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Partial Putaway',
                'count' => $partial_put,
                'percentage' => round($total ? ($partial_put / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Full Putaway',
                'count' => $full_put,
                'percentage' => round($total ? ($full_put / $total) * 100 : 0, 1)
            ],
        ];
    }

    protected function getDoData()
    {
        $total = DeliveryOrder::count();
        $un = DeliveryOrder::whereStatus(DeliveryOrder::STATUS_UNALLOCATED)->count();
        $partial_pick = DeliveryOrder::whereStatus(DeliveryOrder::STATUS_PARTIAL_PICK)->count();
        $full_pick = DeliveryOrder::whereStatus(DeliveryOrder::STATUS_FULL_PICK)->count();
        $partial_check = DeliveryOrder::whereStatus(DeliveryOrder::STATUS_PARTIALLY_CHECKED)->count();
        $full_check = DeliveryOrder::whereStatus(DeliveryOrder::STATUS_FULLY_CHECKED)->count();
        $gi = DeliveryOrder::whereStatus(DeliveryOrder::STATUS_GOOD_ISSUED)->count();

        return [
            [
                'label' => 'Unallocated',
                'count' => $un,
                'percentage' => round($total ? ($un / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Partial Pick',
                'count' => $partial_pick,
                'percentage' => round($total ? ($partial_pick / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Full Pick',
                'count' => $full_pick,
                'percentage' => round($total ? ($full_pick / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Partially Checked',
                'count' => $partial_check,
                'percentage' => round($total ? ($partial_check / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Fully Checked',
                'count' => $full_check,
                'percentage' => round($total ? ($full_check / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Good Issue',
                'count' => $gi,
                'percentage' => round($total ? ($gi / $total) * 100 : 0, 1)
            ],
        ];
    }

    protected function getOutboundData()
    {
        $total = OutboundDelivery::count();
        $un = OutboundDelivery::whereStatus(OutboundDelivery::STATUS_UNCOMMITTED)->count();
        $partial = OutboundDelivery::whereStatus(OutboundDelivery::STATUS_PARTIALLY_COMMITTED)->count();
        $full = OutboundDelivery::whereStatus(OutboundDelivery::STATUS_FULLY_COMMITTED)->count();
        
        return [
            [
                'label' => 'Uncommitted',
                'count' => $un,
                'percentage' => round($total ? ($un / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Partially Committed',
                'count' => $partial,
                'percentage' => round($total ? ($partial / $total) * 100 : 0, 1)
            ],
            [
                'label' => 'Fully Committed',
                'count' => $full,
                'percentage' => round($total ? ($full / $total) * 100 : 0, 1)
            ]
        ];
    }
}
