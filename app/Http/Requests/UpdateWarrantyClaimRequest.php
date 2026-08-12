<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarrantyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'                    => 'required|in:pending,under_inspection,sent_to_vendor,repaired,replaced,rejected,completed',
            'action_taken'              => 'nullable|in:none,repair,replacement,refund',
            'replacement_serial_number' => 'nullable|string|max:100',
            'note'                      => 'nullable|string|max:500',
            'remarks'                   => 'nullable|string|max:500',
        ];
    }
}
