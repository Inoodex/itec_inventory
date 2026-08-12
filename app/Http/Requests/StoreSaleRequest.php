<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_type'          => 'required|in:new,existing',
            'existing_client_id'   => 'nullable|required_if:client_type,existing|exists:customers,id',
            'name'                 => 'nullable|required_if:client_type,new|string',
            'phone'                => 'nullable|required_if:client_type,new|string',
            'address'              => 'nullable|required_if:client_type,new|string',
            'product'              => 'required|array',
            'product.*'            => 'required|integer|exists:products,id',
            'qty'                  => 'required|array',
            'qty.*'                => 'required|numeric|min:1',
            'unit_price'           => 'required|array',
            'unit_price.*'         => 'required|numeric|min:1',
            'subTotal'             => 'required|numeric|min:0',
            'discount'             => 'nullable|numeric|min:0',
            'grandTotal'           => 'required|numeric|min:0',
            'advanced_payment'     => 'nullable|numeric|min:0',
            'duePayment'           => 'nullable|numeric|min:0',
            'vat'                  => 'nullable|numeric|min:0',
            'tax'                  => 'nullable|numeric|min:0',
            'delivery_charge'      => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_type.required'        => 'Please select a client type.',
            'existing_client_id.required_if' => 'Please select an existing customer.',
            'name.required_if'            => 'Customer name is required for new clients.',
            'phone.required_if'           => 'Phone number is required for new clients.',
            'product.required'            => 'At least one product is required.',
            'subTotal.required'           => 'Sub total is required.',
            'grandTotal.required'         => 'Grand total is required.',
        ];
    }
}
