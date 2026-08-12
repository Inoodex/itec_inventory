<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'client_type'    => 'required|in:new,existing',
            'project_name'   => 'required|string|max:255',
            'budget'         => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'status'         => 'required|in:pending,in_progress,completed,cancelled',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
        ];

        if ($this->input('client_type') === 'new') {
            $rules['client_name']    = 'required|string|max:255';
            $rules['client_phone']   = 'required|string|max:20';
            $rules['client_email']   = 'required|email|max:255';
            $rules['client_address'] = 'required|string|max:500';
        } else {
            $rules['existing_client_id'] = 'required|exists:clients,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'client_type.required'           => 'Please select a client type.',
            'existing_client_id.required'    => 'Please select an existing client.',
            'existing_client_id.exists'      => 'Selected client does not exist.',
            'project_name.required'          => 'Project name is required.',
            'status.required'                => 'Please select a project status.',
            'status.in'                      => 'Invalid project status selected.',
            'start_date.required'            => 'Start date is required.',
            'end_date.after_or_equal'        => 'End date must be on or after the start date.',
            'client_name.required'           => 'Client name is required.',
            'client_phone.required'          => 'Client phone number is required.',
            'client_email.required'          => 'Client email is required.',
            'client_email.email'             => 'Please enter a valid email address.',
            'client_address.required'        => 'Client address is required.',
        ];
    }
}
