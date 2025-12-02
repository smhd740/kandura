<?php

namespace App\Http\Requests\Auth;

use id;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore(auth()->id()),
            ],
            'profile_image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'phone.unique' => 'Phone already exists',
            'profile_image.image' => 'File must be an image',
            'profile_image.mimes' => 'Image must be jpeg, png, or jpg',
            'profile_image.max' => 'Image size must not exceed 2MB',
        ];
    }
}
