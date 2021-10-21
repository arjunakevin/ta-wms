<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Http\Request;
use App\Models\InboundDelivery;

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
            'gr_data' => $this->getGrData()
        ];

        return inertia()->render('Dashboard', compact('data'));
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
                'percentage' => ($unreceived / $total) * 100
            ],
            [
                'label' => 'Partially Received',
                'count' => $partial,
                'percentage' => ($partial / $total) * 100
            ],
            [
                'label' => 'Fully Received',
                'count' => $full,
                'percentage' => ($full / $total) * 100
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

        return [
            [
                'label' => 'Draft',
                'count' => $draft,
                'percentage' => ($draft / $total) * 100
            ],
            [
                'label' => 'Partially Checked',
                'count' => $partial_check,
                'percentage' => ($partial_check / $total) * 100
            ],
            [
                'label' => 'Fully Checked',
                'count' => $full_check,
                'percentage' => ($full_check / $total) * 100
            ],
            [
                'label' => 'Received',
                'count' => $received,
                'percentage' => ($received / $total) * 100
            ],
        ];
    }
}
