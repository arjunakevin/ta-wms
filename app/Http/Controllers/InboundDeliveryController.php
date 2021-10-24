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
    public function index(Request $request)
    {
        $status = $request->status ?? 'open';

        $data = InboundDelivery::with('client')
            ->withCount('details')
            ->when($request->id, function ($query) use ($request) {
                $query->whereId($request->id);
            })
            ->when($request->reference, function ($query) use ($request) {
                $query->where('reference', 'like', '%' . $request->reference . '%');
            })
            ->when($request->client_code, function ($query) use ($request) {
                $query->whereHas('client', function ($query) use ($request) {
                    $query->whereCode($request->client_code);
                });
            })
            ->when($request->arrival_date_from && $request->arrival_date_to, function ($query) use ($request) {
                $query->whereBetween('arrival_date', [$request->arrival_date_from, $request->arrival_date_to]);
            })
            ->when($request->po_date_from && $request->po_date_to, function ($query) use ($request) {
                $query->whereBetween('po_date', [$request->po_date_from, $request->po_date_to]);
            })
            ->when($status, function ($query) use ($status) {
                if ($status != 'open') {
                    $op = '=';
                } else {
                    $op = '!=';
                }

                $query->where('status', $op, InboundDelivery::STATUS_FULLY_RECEIVED);
            })
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
        if ($inbound->isProcessed()) {
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
