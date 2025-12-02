<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDesignOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // ✅ استخدم isAdmin() بدل can()
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name.ar' => ['sometimes', 'required', 'string', 'max:255'],
            'name.en' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::in(['color', 'fabric_type', 'sleeve_type', 'dome_type'])],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name.ar' => 'الاسم بالعربي',
            'name.en' => 'الاسم بالإنجليزي',
            'type' => 'النوع',
            'image' => 'الصورة',
            'is_active' => 'الحالة',
        ];
    }

    public function messages(): array
    {
        return [
            'name.ar.required' => 'الاسم بالعربي مطلوب',
            'name.en.required' => 'الاسم بالإنجليزي مطلوب',
            'type.required' => 'النوع مطلوب',
            'type.in' => 'النوع المختار غير صحيح',
            'image.image' => 'يجب أن يكون الملف صورة',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ];
    }
}
