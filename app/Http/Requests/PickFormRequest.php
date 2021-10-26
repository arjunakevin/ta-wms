<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class PickFormRequest extends FormRequest
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
