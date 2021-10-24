<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OutboundDeliveryFormRequest extends FormRequest
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
                Rule::unique('outbound_deliveries', 'reference')->where(function ($q) {
                    $q->whereClientId($this->client_id);
                })->ignore($this->id, 'id')
            ],
            'client_id' => 'required|exists:clients,id',
            'destination_name' => 'required|max:255',
            'destination_phone' => 'required|max:255',
            'destination_address_1' => 'required',
            'destination_address_2' => 'nullable',
            'request_delivery_date' => 'required|date',
            'po_reference' => 'nullable',
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
        $outbound_id = optional($this->outbound)->id;

        $this->merge([
            'id' => $outbound_id,
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
