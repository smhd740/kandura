<?php

namespace App\Http\Requests\Design;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            // Name - translatable
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            // Description - translatable
            'description' => ['required', 'array'],
            'description.ar' => ['required', 'string'],
            'description.en' => ['required', 'string'],

            // Price
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],

            // Images - at least one
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB

            // Primary image index
            'primary_image_index' => ['nullable', 'integer', 'min:0'],

            // Sizes - at least one
            'measurement_ids' => ['required', 'array', 'min:1'],
            'measurement_ids.*' => ['required', 'exists:measurements,id'],

            // Design Options
            'design_option_ids' => ['nullable', 'array'],
            'design_option_ids.*' => ['exists:design_options,id'],
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'Design name is required',
            'name.ar.required' => 'Arabic name is required',
            'name.en.required' => 'English name is required',

            // Description
            'description.required' => 'Design description is required',
            'description.ar.required' => 'Arabic description is required',
            'description.en.required' => 'English description is required',

            // Price
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price cannot be negative',

            // Images
            'images.required' => 'At least one image is required',
            'images.min' => 'At least one image is required',
            'images.max' => 'Maximum 10 images allowed',
            'images.*.image' => 'File must be an image',
            'images.*.mimes' => 'Image must be jpeg, png, jpg, gif, or webp',
            'images.*.max' => 'Image size cannot exceed 5MB',

            // Primary image
            'primary_image_index.integer' => 'Primary image index must be a number',
            'primary_image_index.min' => 'Primary image index cannot be negative',

            // Sizes
            'measurement_ids.required' => 'At least one size is required',
            'measurement_ids.min' => 'At least one size is required',
            'measurement_ids.*.exists' => 'Selected size does not exist',

            // Options
            'design_option_ids.*.exists' => 'Selected design option does not exist',
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('primary_image_index')) {
            $this->merge([
                'primary_image_index' => 0,
            ]);
        }
    }
}
