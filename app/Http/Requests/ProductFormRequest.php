<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ProductFormRequest extends FormRequest
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
        $product_id = optional($this->product)->id;
        
        return [
            'code' => 'required|max:255|unique:products,code,' . $product_id,
            'barcode' => 'nullable|sometimes|max:255|unique:products,barcode,' . $product_id,
            'client_code' => 'required|exists:clients,code',
            'description_1' => 'required',
            'description_2' => 'nullable',
            'is_active' => 'required|boolean'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $validator->after(function ($validator) {
                $this->merge([
                    'client_id' => Client::whereCode($this->client_code)->firstOrFail()->id,
                ]);
            });
        }
    }
}
