<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Validation\Rule;
use App\Models\OutboundDelivery;
use Illuminate\Foundation\Http\FormRequest;

class OutboundDeliveryDetailFormRequest extends FormRequest
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
            'outbound_delivery_id' => 'required|exists:outbound_deliveries,id',
            'line_id' => [
                'required',
                Rule::unique('outbound_delivery_details', 'line_id')->where(function ($q) {
                    $q->whereOutboundDeliveryId($this->outbound_delivery_id);
                })->ignore($this->id, 'id')
            ],
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
                    ->where('client_id', $this->client_id),
                Rule::unique('outbound_delivery_details', 'product_id')->where(function ($q) {
                    $q->whereOutboundDeliveryId($this->outbound_delivery_id);
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
        $outbound = OutboundDelivery::whereId($this->outbound_delivery_id)->first();
        $product = Product::findByCodeOrBarcode($this->code)->first();

        $this->merge([
            'client_id' => $outbound ? $outbound->client_id : -1,
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
