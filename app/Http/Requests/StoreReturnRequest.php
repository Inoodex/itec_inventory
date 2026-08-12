<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id'              => 'required|exists:sales,id',
            'customer_id'          => 'required|exists:customers,id',
            'return_date'          => 'required|date',
            'reason'               => 'required|string|max:1000',
            'notes'                => 'nullable|string|max:1000',
            'items'                => 'required|array|min:1',
            'items.*.sales_item_id'=> 'required|exists:sales_items,id',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'sale_id.required'             => 'Please select the original sale.',
            'sale_id.exists'               => 'Selected sale does not exist.',
            'customer_id.required'         => 'Customer is required.',
            'return_date.required'         => 'Return date is required.',
            'reason.required'              => 'Please provide a reason for the return.',
            'items.required'               => 'At least one item must be selected for return.',
            'items.min'                    => 'At least one item must be selected for return.',
            'items.*.quantity.min'         => 'Return quantity must be at least 1.',
            'items.*.unit_price.min'       => 'Unit price cannot be negative.',
        ];
    }
}
