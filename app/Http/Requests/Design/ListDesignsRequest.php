<?php

namespace App\Http\Requests\Design;

use Illuminate\Foundation\Http\FormRequest;

class ListDesignsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // أي مستخدم يمكنه البحث
    }

    public function rules(): array
    {
        return [
            // Search
            'search' => ['nullable', 'string', 'max:255'],

            // Sorting
            'sort_by' => ['nullable', 'in:name,price,created_at'],
            'sort_order' => ['nullable', 'in:asc,desc'],

            // Pagination
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],

            // Filters - Basic
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['exists:measurements,id'],

            // Filters 
            'design_options' => ['nullable', 'array'],
            'design_options.*' => ['exists:design_options,id'],
            'creator_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Search must be a valid text.',
            'search.max' => 'Search cannot exceed 255 characters.',
            'sort_by.in' => 'Sort by must be one of: name, price, created_at.',
            'sort_order.in' => 'Sort order must be either asc or desc.',
            'per_page.integer' => 'Per page must be a number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'min_price.numeric' => 'Minimum price must be a number.',
            'min_price.min' => 'Minimum price cannot be negative.',
            'max_price.numeric' => 'Maximum price must be a number.',
            'max_price.min' => 'Maximum price cannot be negative.',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
            'measurements.array' => 'Measurements must be an array.',
            'measurements.*.exists' => 'Selected measurement does not exist.',
            'design_options.array' => 'Design options must be an array.',
            'design_options.*.exists' => 'Selected design option does not exist.',
            'creator_id.exists' => 'Selected creator does not exist.',
        ];
    }
}
