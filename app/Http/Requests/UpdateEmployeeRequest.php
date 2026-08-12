<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:employees,email,' . $employeeId,
            'phone'       => 'required|string|max:20',
            'designation' => 'required|string|max:255',
            'join_date'   => 'required|date',
            'salary'      => 'required|numeric|min:0',
            'status'      => 'required|in:active,inactive',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Employee name is required.',
            'email.required'       => 'Email address is required.',
            'email.email'          => 'Please enter a valid email address.',
            'email.unique'         => 'This email is already used by another employee.',
            'phone.required'       => 'Phone number is required.',
            'designation.required' => 'Designation is required.',
            'join_date.required'   => 'Join date is required.',
            'salary.required'      => 'Salary amount is required.',
            'image.image'          => 'Profile image must be a valid image file.',
            'image.max'            => 'Profile image must not exceed 2MB.',
        ];
    }
}
