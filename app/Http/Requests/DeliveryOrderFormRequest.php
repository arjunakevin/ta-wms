<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryOrderFormRequest extends FormRequest
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
        $id = optional($this->delivery_order)->id;

        return [
            'outbound_delivery_id' => 'required|exists:outbound_deliveries,id',
            'reference' => [
                'required',
                'max:255',
                Rule::unique('delivery_orders', 'reference')->where(function ($q) {
                    $q->whereOutboundDeliveryId($this->outbound_delivery_id);
                })->ignore($id, 'id')
            ],
            'notes' => 'nullable'
        ];
    }
}
