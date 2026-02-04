<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('addresses')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'street' => ['required', 'string', 'max:255'],
            'building_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['nullable', 'string', 'max:50'],  // ✅ شلنا unique - مش منطقي يكون unique
            'details' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Address name is required',
            'name.unique' => 'You already have an address with this name',
            'city_id.required' => 'City is required',
            'city_id.exists' => 'Selected city does not exist',
            'street.required' => 'Street is required',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.between' => 'Longitude must be between -180 and 180',
        ];
    }
}
