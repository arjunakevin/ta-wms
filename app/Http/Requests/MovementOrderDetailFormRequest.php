<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Location;
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
        $product = Product::whereCode($this->product_code)->first();
        $location = Location::whereCode($this->location_code)->first();

        $this->merge([
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
            $inventory = $this->movement_order->documentable->inventories()
                ->whereHas('product', function ($q) {
                    $q->whereId($this->product_id);
                })
                ->first();

            if (!$inventory) {
                $validator->errors()->add('product_id', 'The selected product is invalid.');
            } else if ($inventory->available_pick_quantity <= 0) {
                $validator->errors()->add('product_id', 'The selected product has no putaway outstanding.');
            } else if ($inventory->available_pick_quantity < $this->base_quantity) {
                $validator->errors()->add('base_quantity', "Base quantity can't be greater than remaining quantity (Remaining: {$inventory->available_pick_quantity}).");
            } else {
                $this->merge([
                    'inventory' => $inventory
                ]);
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
