<?php

namespace App\Http\Controllers;

use App\Models\GoodReceive;
use Illuminate\Http\Request;
use App\Models\InboundDelivery;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\GoodReceiveFormRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\GoodReceiveCheckFormRequest;
use App\Exceptions\GoodReceiveInboundDeliveryException;

class GoodReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = GoodReceive::with('inbound_delivery.client')
            ->paginate();

        return inertia()->render('GoodReceive/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\InboundDelivery  $inbound
     * @return \Illuminate\Http\Response
     */
    public function create(InboundDelivery $inbound)
    {
        $details = $inbound->open_details()
            ->with('product')
            ->get();

        $inbound->load('client');

        return inertia()->render('GoodReceive/Form', compact('inbound', 'details'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\GoodReceiveFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GoodReceiveFormRequest $request)
    {
        $gr = DB::transaction(function () use ($request) {
            return tap(
                GoodReceive::create($request->validated()),
                function ($gr) {
                    foreach ($gr->inbound_delivery->open_details as $detail) {
                        $gr->createDetail($detail);
                    }

                    $gr->inbound_delivery->updateStatus();
                }
            );
        });

        return redirect()->route('grs.show', $gr);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function show(GoodReceive $good_receive)
    {
        $good_receive->load('inbound_delivery.client', 'inventories');

        $details = $good_receive->details()
            ->with('inbound_delivery_detail.product', 'inventory')
            ->get();

        return inertia()->render('GoodReceive/Detail', compact('good_receive', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function edit(GoodReceive $good_receive)
    {
        $inbound = $good_receive->inbound_delivery->load('client');
        $details = $good_receive->details()
            ->with('inbound_delivery_detail.product')
            ->get();

        return inertia()->render('GoodReceive/Form', compact('good_receive', 'inbound', 'details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\GoodReceiveFormRequest  $request
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function update(GoodReceiveFormRequest $request, GoodReceive $good_receive)
    {
        $good_receive->update($request->validated());

        return redirect()->route('grs.show', $good_receive);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GoodReceive  $good_receive
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoodReceive $good_receive)
    {
        if ($good_receive->processed()) {
            throw ValidationException::withMessages([
                'message' => 'Good receive is already processed.'
            ]);
        }

        DB::transaction(function () use ($good_receive) {
            foreach ($good_receive->details as $detail) {
                $detail->restoreInboundDeliveryDetailOpenQuantity();
            }
    
            $good_receive->delete();

            $good_receive->inbound_delivery->updateStatus();
        });

        return redirect()->route('grs.index');
    }

    /**
     * Search inbound delivery.
     *
     * @param Request  $request
     * @return void
     */
    public function searchInbound(Request $request)
    {
        try {
            $inbound = InboundDelivery::whereReference($request->reference)
                ->whereHas('client', function ($q) use ($request) {
                    $q->whereCode($request->client_code);
                })->first();
    
            if (!$inbound) {
                throw new GoodReceiveInboundDeliveryException('Inbound delivery data not found.');
            } else if ($inbound->status == InboundDelivery::STATUS_FULLY_RECEIVED) {
                throw new GoodReceiveInboundDeliveryException('Inbound delivery is fully received.');
            }

            return redirect()->route('grs.create', $inbound);
        } catch (GoodReceiveInboundDeliveryException $e) {
            throw ValidationException::withMessages([
                'inbound' => $e->getMessage()
            ]);
        }
    }

    /**
     * Good receive check index.
     *
     * @return \Illuminate\Http\Response
     */
    public function check(GoodReceive $good_receive)
    {
        $details = $good_receive->details()
            ->with('inbound_delivery_detail.product')
            ->paginate();

        $good_receive->load('inbound_delivery');
        
        return inertia()->render('GoodReceive/Check', compact('good_receive', 'details'));
    }

    /**
     * Submit good receive check.
     *
     * @param \App\Http\Requests\GoodReceiveCheckFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function submitCheck(GoodReceiveCheckFormRequest $request, GoodReceive $good_receive)
    {
        $request->detail->decrement('open_check_quantity', $request->base_quantity);

        $good_receive->updateCheckStatus();

        return redirect()->route('grs.check', $good_receive);
    }

    public function receive(GoodReceive $good_receive)
    {
        if ($good_receive->status == GoodReceive::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'message' => 'Good receive is not checked.'
            ]);
        } else if ($good_receive->status >= GoodReceive::STATUS_RECEIVED || $good_receive->status == GoodReceive::STATUS_FULL_PUTAWAY) {
            throw ValidationException::withMessages([
                'message' => 'Good receive is already received.'
            ]);
        }

        DB::transaction(function () use ($good_receive) {
            $good_receive->receive();
        });

        session()->flash('message', 'Receive success.');

        return redirect()->route('grs.show', $good_receive);
    }
}
