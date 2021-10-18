<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovementOrderFormRequest extends FormRequest
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
        $id = optional($this->movement_order)->id;
        
        return [
            'reference' => 'required|max:255|unique:movement_orders,reference,' . $id,
            'document_id' => 'required',
            'date' => 'required|date',
            'type' => 'required|in:1,2' // 1 = Putaway, 2 = Picking
        ];
    }
}
