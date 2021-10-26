<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PicklistResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $details = $this->open_details->unique(function ($item) {
            return $item['product_id'].$item['source_location_id'];
        })
        ->transform(function ($detail) {
            $detail->base_quantity = $this->open_details
                ->where('product_id', $detail->product_id)
                ->where('source_location_id', $detail->source_location_id)
                ->sum('base_quantity');

            return $detail;
        });

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'date' => $this->date,
            'client_code' => $this->documentable->outbound_delivery->client->code,
            'outstanding' => $details->map(function ($detail) {
                $product = $detail->product;
                
                return [
                    'product_code' => $product->code,
                    'description' => $product->description_1,
                    'base_quantity' => $detail->base_quantity . ' ' . $product->uom_name,
                    'location_code' => $detail->source_location->code
                ];
            })->values()->all()
        ];
    }
}
