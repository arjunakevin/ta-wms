<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GoodReceiveCheckFormRequest extends FormRequest
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
            'base_quantity' => 'required|numeric|gt:0'
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

        $this->merge([
            'product_id' => $product ? $product->id : -1
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
            $detail = $this->good_receive->details()
                ->whereHas('inbound_delivery_detail', function ($q) {
                    $q->whereProductId($this->product_id);
                })
                ->first();

            if (!$detail) {
                $validator->errors()->add('product_id', 'The selected product is invalid.');
            } else if ($detail->open_check_quantity <= 0) {
                $validator->errors()->add('product_id', 'The selected product is fully checked.');
            } else if ($detail->open_check_quantity < $this->base_quantity) {
                $validator->errors()->add('base_quantity', "Check quantity can't be greater than remaining quantity (Remaining: {$detail->open_check_quantity}).");
            } else {
                $this->merge([
                    'detail' => $detail
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
            'base_quantity' => 'check quantity'
        ];
    }
}
