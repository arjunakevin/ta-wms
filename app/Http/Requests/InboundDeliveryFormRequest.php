<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InboundDeliveryFormRequest extends FormRequest
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
            'reference' => [
                'required',
                'max:255',
                Rule::unique('inbound_deliveries', 'reference')->where(function ($q) {
                    $q->whereClientId($this->client_id);
                })->ignore($this->id, 'id')
            ],
            'client_id' => 'required|exists:clients,id',
            'arrival_date' => 'nullable|sometimes|date',
            'po_date' => 'nullable|sometimes|date',
            'notes' => 'nullable'
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
        $inbound_id = optional($this->inbound)->id;

        $this->merge([
            'id' => $inbound_id,
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
