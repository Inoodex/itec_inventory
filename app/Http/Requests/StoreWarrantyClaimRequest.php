<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sales_item_id'       => 'required|exists:sales_items,id',
            'serial_number'       => 'nullable|string|max:100',
            'claim_date'          => 'required|date',
            'problem_description' => 'required|string|max:1000',
            'condition_notes'     => 'nullable|string|max:500',
            'remarks'             => 'nullable|string|max:500',
        ];
    }
}
