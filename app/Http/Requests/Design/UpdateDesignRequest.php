<?php

namespace App\Http\Requests\Design;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Name - translatable 
            'name' => ['sometimes', 'array'],
            'name.ar' => ['required_with:name', 'string', 'max:255'],
            'name.en' => ['required_with:name', 'string', 'max:255'],

            // Description - translatable
            'description' => ['sometimes', 'array'],
            'description.ar' => ['required_with:description', 'string'],
            'description.en' => ['required_with:description', 'string'],

            // Price
            'price' => ['sometimes', 'numeric', 'min:0', 'max:9999999.99'],

            // Images
            'images' => ['sometimes', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],

            // Sizes
            'measurement_ids' => ['sometimes', 'array', 'min:1'],
            'measurement_ids.*' => ['required', 'exists:measurements,id'],

            // Design Options
            'design_option_ids' => ['sometimes', 'array'],
            'design_option_ids.*' => ['exists:design_options,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.ar.required_with' => 'Arabic name is required when updating name',
            'name.en.required_with' => 'English name is required when updating name',
            'description.ar.required_with' => 'Arabic description is required when updating description',
            'description.en.required_with' => 'English description is required when updating description',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price cannot be negative',
            'images.min' => 'At least one image is required',
            'images.*.image' => 'File must be an image',
            'images.*.mimes' => 'Image must be jpeg, png, jpg, or gif',
            'images.*.max' => 'Image size cannot exceed 5MB',
            'measurement_ids.min' => 'At least one size is required',
            'measurement_ids.*.exists' => 'Selected size does not exist',
            'design_option_ids.*.exists' => 'Selected design option does not exist',
        ];
    }
}
