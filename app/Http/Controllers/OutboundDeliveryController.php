<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutboundDelivery;
use App\Models\OutboundDeliveryDetail;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\OutboundDeliveryFormRequest;
use App\Http\Requests\OutboundDeliveryDetailFormRequest;

class OutboundDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = OutboundDelivery::with('client')
            ->latest()
            ->paginate();

        return inertia()->render('OutboundDelivery/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return inertia()->render('OutboundDelivery/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OutboundDeliveryFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutboundDeliveryFormRequest $request)
    {
        $outbound = OutboundDelivery::create($request->validated());

        return redirect()->route('outbounds.edit', $outbound);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OutboundDelivery  $outbound
     * @return \Illuminate\Http\Response
     */
    public function show(OutboundDelivery $outbound)
    {
        $outbound->load('client');

        $details = $outbound->details()->with('product')->orderBy('id')->get();

        return inertia()->render('OutboundDelivery/Detail', compact('outbound', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OutboundDelivery  $outbound
     * @return \Illuminate\Http\Response
     */
    public function edit(OutboundDelivery $outbound)
    {
        $outbound->load('client');

        $details = $outbound->details()->with('product')->orderBy('id')->get();

        return inertia()->render('OutboundDelivery/Form', compact('outbound', 'details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OutboundDeliveryFormRequest  $request
     * @param  \App\Models\OutboundDelivery  $outbound
     * @return \Illuminate\Http\Response
     */
    public function update(OutboundDeliveryFormRequest $request, OutboundDelivery $outbound)
    {
        $outbound->update($request->validated());

        return redirect()->route('outbounds.edit', $outbound);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OutboundDelivery  $outbound
     * @return \Illuminate\Http\Response
     */
    public function destroy(OutboundDelivery $outbound)
    {
        if ($outbound->isProcessed()) {
            throw ValidationException::withMessages([
                'message' => 'Outbound is already received.'
            ]);
        }
    
        $outbound->delete();

        return redirect()->route('outbounds.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\OutboundDeliveryDetailFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDetail(OutboundDeliveryDetailFormRequest $request)
    {
        $detail = OutboundDeliveryDetail::create($request->validated());

        $detail->outbound_delivery->updateStatus();

        return redirect()->route('outbounds.edit', $detail->outbound_delivery);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\OutboundDeliveryDetailFormRequest  $request
     * @param  \App\Models\OutboundDeliveryDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function updateDetail(OutboundDeliveryDetailFormRequest $request, OutboundDeliveryDetail $detail)
    {
        $detail->update($request->validated());

        $detail->outbound_delivery->updateStatus();

        return redirect()->route('outbounds.edit', $detail->outbound_delivery);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OutboundDeliveryDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function destroyDetail(OutboundDeliveryDetail $detail)
    {
        if ($detail->isCommitted()) {
            throw ValidationException::withMessages([
                'line_id' => 'This line is already committed.'
            ]);
        }

        $detail->delete();

        $detail->outbound_delivery->updateStatus();

        return redirect()->route('outbounds.edit', $detail->outbound_delivery);
    }
}
