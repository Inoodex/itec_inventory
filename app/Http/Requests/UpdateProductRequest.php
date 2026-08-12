<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_id'         => 'required|exists:brands,id',
            'category_id'      => 'nullable|exists:categories,id',
            'name'             => 'required|string|max:255',
            'model_name'       => 'required|string|max:255',
            'barcode'          => 'nullable|string|max:100|unique:products,barcode,' . ($this->product->id ?? $this->route('product')?->id ?? $this->route('product')),
            'warranty'         => 'nullable|integer|min:0',
            'status'           => 'required|boolean',
            'is_serialized'    => 'nullable|boolean',
            'photos'           => 'nullable|array',
            'photos.*'         => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remaining_photos' => 'nullable|string', // JSON of kept photos
        ];
    }

    public function messages(): array
    {
        return [
            'brand_id.required'   => 'Please select a brand.',
            'name.required'       => 'Product name is required.',
            'model_name.required' => 'Model name is required.',
            'photos.*.image'      => 'Each photo must be a valid image.',
            'photos.*.max'        => 'Each photo must not exceed 2MB.',
        ];
    }
}
