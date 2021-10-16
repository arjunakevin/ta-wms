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
            'client_id' => 'required|exists:clients,id',
            'description_1' => 'required',
            'description_2' => 'nullable',
            'is_active' => 'required|boolean'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $client = Client::whereCode($this->client_code)->first();

        $this->merge([
            'client_id' => $client ? $client->id : -1
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
            'client_id' => 'client'
        ];
    }
}
