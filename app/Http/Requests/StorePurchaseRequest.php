<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'     => 'required|exists:products,id',
            'vendor_id'      => 'required|exists:vendors,id',
            'quantity'       => 'required|numeric|min:1',
            'unit_price'     => 'required|numeric|min:0',
            'sub_price'      => 'nullable|numeric|min:0',
            'total_price'    => 'required|numeric|min:0',
            'payment'        => 'required|numeric|min:0',
            'due'            => 'required|numeric|min:0',
            'serial_numbers' => 'nullable|array',
            'serial_numbers.*' => 'string|max:100',
            'serial_bulk'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'  => 'Please select a product.',
            'product_id.exists'    => 'Selected product does not exist.',
            'vendor_id.required'   => 'Please select a vendor.',
            'vendor_id.exists'     => 'Selected vendor does not exist.',
            'quantity.required'    => 'Quantity is required.',
            'quantity.min'         => 'Quantity must be at least 1.',
            'unit_price.required'  => 'Unit price is required.',
            'total_price.required' => 'Total price is required.',
            'payment.required'     => 'Payment amount is required.',
            'due.required'         => 'Due amount is required.',
        ];
    }
}
