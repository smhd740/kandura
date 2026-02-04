<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ✅ تأكد إنه العنوان ملك اليوزر الحالي
        return $this->address->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                // ✅ unique per user مع تجاهل العنوان الحالي
                Rule::unique('addresses')
                    ->where(function ($query) {
                        return $query->where('user_id', auth()->id());
                    })
                    ->ignore($this->address->id)  // ✅ تجاهل العنوان اللي عم نعدله
            ],
            'city_id' => ['sometimes', 'integer', 'exists:cities,id'],
            'district' => ['nullable', 'string', 'max:255'],          // ✅ مضاف
            'street' => ['sometimes', 'string', 'max:255'],
            'building_number' => ['nullable', 'string', 'max:50'],
            'house_number' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],              // ✅ مضاف
            'postal_code' => ['nullable', 'string', 'max:20'],        // ✅ مضاف
            'details' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Address name must not exceed 100 characters',
            'name.unique' => 'You already have an address with this name',  // ✅ مضاف
            'city_id.exists' => 'Selected city does not exist',
            'street.max' => 'Street must not exceed 255 characters',
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.between' => 'Longitude must be between -180 and 180',
        ];
    }
}
