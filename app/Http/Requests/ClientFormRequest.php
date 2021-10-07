<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFormRequest extends FormRequest
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
        $client_id = optional($this->client)->id;
        
        return [
            'code' => 'required|max:255|unique:clients,code,' . $client_id,
            'name' => 'required|max:255',
            'address_1' => 'required',
            'address_2' => 'nullable'
        ];
    }
}
