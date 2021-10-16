<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GoodReceiveFormRequest extends FormRequest
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
        $id = optional($this->good_receive)->id;

        return [
            'inbound_delivery_id' => 'required|exists:inbound_deliveries,id',
            'reference' => [
                'required',
                'max:255',
                Rule::unique('good_receives', 'reference')->where(function ($q) {
                    $q->whereInboundDeliveryId($this->inbound_delivery_id);
                })->ignore($id, 'id')
            ],
            'receive_date' => 'required|date',
            'notes' => 'nullable'
        ];
    }
}
