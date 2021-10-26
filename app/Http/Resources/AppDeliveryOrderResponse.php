<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppDeliveryOrderResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'client_code' => $this->outbound_delivery->client->code,
            'status' => $this->getStatusLabel(),
            'notes' => $this->notes ?? '',
            'outstanding' => $this->details->where('open_check_quantity', '>', 0)->map(function ($detail) {
                $product = $detail->outbound_delivery_detail->product;
                
                return [
                    'product_code' => $product->code,
                    'description' => $product->description_1,
                    'base_quantity' => $detail->open_check_quantity . ' ' . $product->uom_name
                ];
            })->values()->all()
        ];
    }
}
