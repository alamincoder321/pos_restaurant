<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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
        $rules = [
            'sale.invoice' => 'required',
            'sale.date' => 'required',
            'sale.paid' => 'required',
            'carts' => 'required|array'
        ];

        if ($this->customer['type'] == 'new') {
            $rules['customer.name'] = 'required';
            $rules['customer.phone'] = 'required';
        }
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'sale.date.required' => 'Sale date required',
            'sale.paid.required' => 'Sale paid required',
            'customer.name.required' => 'Customer name required',
            'customer.phone.required' => 'Customer phone required',
        ];
    }
}
