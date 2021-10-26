<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
        $user_id = optional($this->user)->id;
        
        $rules = [
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username,' . $user_id,
            'client_id' => 'nullable|sometimes|exists:clients,id',
            'email' => 'required|max:255|email|unique:users,email,' . $user_id
        ];
        
        if (!$user_id) {
            $rules['password'] = 'required';
        }
        
        return $rules;
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
            'client_id' => $client ? $client->id : null
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
