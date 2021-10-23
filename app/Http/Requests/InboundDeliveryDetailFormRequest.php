<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\InboundDelivery;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InboundDeliveryDetailFormRequest extends FormRequest
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
            'inbound_delivery_id' => 'required|exists:inbound_deliveries,id',
            'line_id' => [
                'required',
                Rule::unique('inbound_delivery_details', 'line_id')->where(function ($q) {
                    $q->whereInboundDeliveryId($this->inbound_delivery_id);
                })->ignore($this->id, 'id')
            ],
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('client_id', $this->client_id),
                Rule::unique('inbound_delivery_details', 'product_id')->where(function ($q) {
                    $q->whereInboundDeliveryId($this->inbound_delivery_id);
                })->ignore($this->id, 'id')
            ],
            'base_quantity' => 'required|numeric|min:1',
            'open_quantity' => 'required|numeric|gte:0'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $inbound = InboundDelivery::whereId($this->inbound_delivery_id)->first();
        $product = Product::whereCode($this->code)->first();

        $this->merge([
            'client_id' => $inbound ? $inbound->client_id : -1,
            'product_id' => $product ? $product->id : -1,
            'open_quantity' => $this->detail
                ? ($this->base_quantity - $this->detail->base_quantity) + $this->detail->open_quantity
                : $this->base_quantity
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
            'product_id' => 'product'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id.unique' => 'Product already exists.'
        ];
    }
}
