<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppPutawayResponse extends JsonResource
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
            'client_code' => $this->inbound_delivery->client->code,
            'status' => $this->getStatusLabel(),
            'notes' => $this->notes ?? '',
            'outstanding' => $this->putaway_list->map(function ($inventory) {
                $product = $inventory->product;

                return [
                    'product_code' => $product->code,
                    'description' => $product->description_1,
                    'base_quantity' => $inventory->available_pick_quantity . ' ' . $product->uom_name
                ];
            })->values()->all()
        ];
    }
}
