<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Customer name is required.',
            'phone.required' => 'Phone number is required.',
            'email.email'    => 'Please enter a valid email address.',
        ];
    }
}
