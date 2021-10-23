<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InboundDelivery;
use Illuminate\Support\Facades\DB;
use App\Models\InboundDeliveryDetail;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\InboundDeliveryFormRequest;
use App\Http\Requests\InboundDeliveryDetailFormRequest;

class InboundDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = InboundDelivery::with('client')
            ->withCount('details')
            ->paginate();

        return inertia()->render('InboundDelivery/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return inertia()->render('InboundDelivery/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\InboundDeliveryFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InboundDeliveryFormRequest $request)
    {
        $inbound = InboundDelivery::create($request->validated());

        return redirect()->route('inbounds.edit', $inbound);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InboundDelivery  $inbound
     * @return \Illuminate\Http\Response
     */
    public function show(InboundDelivery $inbound)
    {
        $inbound->load('client');

        $details = $inbound->details()->with('product')->orderBy('id')->get();

        return inertia()->render('InboundDelivery/Detail', compact('inbound', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InboundDelivery  $inbound
     * @return \Illuminate\Http\Response
     */
    public function edit(InboundDelivery $inbound)
    {
        $inbound->load('client');

        $details = $inbound->details()->with('product')->orderBy('id')->get();

        return inertia()->render('InboundDelivery/Form', compact('inbound', 'details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\InboundDeliveryFormRequest  $request
     * @param  \App\Models\InboundDelivery  $inbound
     * @return \Illuminate\Http\Response
     */
    public function update(InboundDeliveryFormRequest $request, InboundDelivery $inbound)
    {
        $inbound->update($request->validated());

        return redirect()->route('inbounds.edit', $inbound);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundDelivery  $inbound
     * @return \Illuminate\Http\Response
     */
    public function destroy(InboundDelivery $inbound)
    {
        if ($inbound->processed()) {
            throw ValidationException::withMessages([
                'message' => 'Inbound is already received.'
            ]);
        }
    
        $inbound->delete();

        return redirect()->route('inbounds.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\InboundDeliveryDetailFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDetail(InboundDeliveryDetailFormRequest $request)
    {
        $detail = InboundDeliveryDetail::create($request->validated());

        $detail->inbound_delivery->updateStatus();

        return redirect()->route('inbounds.edit', $detail->inbound_delivery);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\InboundDeliveryDetailFormRequest  $request
     * @param  \App\Models\InboundDeliveryDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function updateDetail(InboundDeliveryDetailFormRequest $request, InboundDeliveryDetail $detail)
    {
        $detail->update($request->validated());

        $detail->inbound_delivery->updateStatus();

        return redirect()->route('inbounds.edit', $detail->inbound_delivery);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundDeliveryDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function destroyDetail(InboundDeliveryDetail $detail)
    {
        if ($detail->isReceived()) {
            throw ValidationException::withMessages([
                'line_id' => 'This line is already received.'
            ]);
        }

        $detail->delete();

        $detail->inbound_delivery->updateStatus();

        return redirect()->route('inbounds.edit', $detail->inbound_delivery);
    }
}
