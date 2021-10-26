<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class MovementOrderDetailFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'base_quantity' => 'required|numeric|gt:0',
            'location_id' => 'required|exists:locations,id'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $product = Product::findByCodeOrBarcode($this->product_code)->first();
        $location = Location::whereCode($this->location_code)->first();

        $this->merge([
            'location' => $location,
            'product_id' => $product ? $product->id : -1,
            'location_id' => $location ? $location->id : -1
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->movement_order->isPutaway()) {
                $inventories = $this->movement_order->documentable->inventories()
                    ->whereHas('product', function ($q) {
                        $q->whereId($this->product_id);
                    })
                    ->get();

                $available = $inventories->sum('available_pick_quantity');
    
                if (!$inventories) {
                    $validator->errors()->add('product_id', 'The selected product is invalid.');
                } else if ($available <= 0) {
                    $validator->errors()->add('product_id', 'The selected product has no putaway outstanding.');
                } else if ($available < $this->base_quantity) {
                    $validator->errors()->add('base_quantity', "Base quantity can't be greater than remaining quantity (Remaining: {$available}).");
                } else if ($this->location->put_blocked) {
                    $validator->errors()->add('location_id', "Can't put to this location (Blocked).");
                } else {
                    $this->merge([
                        'inventories' => $inventories
                    ]);
                }
            } else {
                $detail = $this->movement_order->documentable->details()
                    ->whereHas('outbound_delivery_detail', function ($q) {
                        $q->whereProductId($this->product_id);
                    })
                    ->first();

                $open_pick_quantity = $detail->open_pick_quantity;

                if (!$detail) {
                    $validator->errors()->add('product_id', 'The selected product is invalid.');
                } else if ($open_pick_quantity <= 0) {
                    $validator->errors()->add('product_id', 'The selected product has no pick outstanding.');
                } else if ($this->base_quantity > $open_pick_quantity) {
                    $validator->errors()->add('base_quantity', "Base quantity can't be greater than remaining quantity (Remaining: {$open_pick_quantity}).");
                }  else if ($this->location->pick_blocked) {
                    $validator->errors()->add('location_id', "Can't pick from this location (Blocked).");
                } else {
                    $inventories = Inventory::whereProductId($this->product_id)
                        ->whereLocationId($this->location_id)
                        ->get();
    
                    $available = $inventories->sum('available_pick_quantity');
    
                    if ($available < $this->base_quantity) {
                        $validator->errors()->add('location_id', "Product not available in this location (Available : ${available})");
                    } else {
                        $this->merge([
                            'inventories' => $inventories,
                            'detail' => $detail
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'product_id' => 'product',
            'location_id' => 'location'
        ];
    }
}
