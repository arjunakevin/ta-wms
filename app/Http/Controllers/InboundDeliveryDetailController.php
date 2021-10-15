<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InboundDeliveryDetail;
use App\Http\Requests\InboundDeliveryDetailFormRequest;

class InboundDeliveryDetailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\InboundDeliveryDetailFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InboundDeliveryDetailFormRequest $request)
    {
        $data = $request->validated();
        $data['open_quantity'] = $data['base_quantity'];
        
        $detail = InboundDeliveryDetail::create($data);

        return redirect()->route('inbounds.edit', $detail->inbound_delivery);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\InboundDeliveryDetailFormRequest  $request
     * @param  \App\Models\InboundDeliveryDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function update(InboundDeliveryDetailFormRequest $request, InboundDeliveryDetail $detail)
    {
        $detail->update($request->validated());

        return redirect()->route('inbounds.edit', $detail->inbound_delivery);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InboundDeliveryDetail  $detail
     * @return \Illuminate\Http\Response
     */
    public function destroy(InboundDeliveryDetail $detail)
    {
        $detail->delete();

        return redirect()->route('inbounds.edit', $detail->inbound_delivery);
    }
}
