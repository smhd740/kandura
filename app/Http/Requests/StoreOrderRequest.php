<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'address_id' => 'required|exists:addresses,id',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,wallet,stripe',

            // Items validation
            'items' => 'required|array|min:1',
            'items.*.design_id' => 'required|exists:designs,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',

            // Measurements validation (at least one required) - شلنا distinct
            'items.*.measurement_ids' => 'required|array|min:1',
            'items.*.measurement_ids.*' => 'required|exists:measurements,id',

            // Design Options validation (optional, can be empty array) - شلنا distinct
            'items.*.design_option_ids' => 'nullable|array',
            'items.*.design_option_ids.*' => 'required|exists:design_options,id',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return app()->getLocale() === 'ar' ? [
            // Address
            'address_id.required' => 'يرجى اختيار عنوان التوصيل',
            'address_id.exists' => 'العنوان المحدد غير موجود',

            // Notes
            'notes.max' => 'الملاحظات لا يمكن أن تتجاوز 1000 حرف',

            // Payment Method
            'payment_method.required' => 'يرجى اختيار طريقة الدفع',
            'payment_method.in' => 'طريقة الدفع المحددة غير صحيحة',

            // Items
            'items.required' => 'يجب إضافة عنصر واحد على الأقل للطلب',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل للطلب',
            'items.array' => 'صيغة العناصر غير صحيحة',

            // Design ID
            'items.*.design_id.required' => 'يرجى اختيار التصميم',
            'items.*.design_id.exists' => 'التصميم المحدد غير موجود',

            // Quantity
            'items.*.quantity.required' => 'يرجى تحديد الكمية',
            'items.*.quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً',
            'items.*.quantity.min' => 'الكمية يجب أن تكون 1 على الأقل',
            'items.*.quantity.max' => 'الكمية لا يمكن أن تتجاوز 100',

            // Measurements
            'items.*.measurement_ids.required' => 'يرجى اختيار مقاس واحد على الأقل',
            'items.*.measurement_ids.min' => 'يرجى اختيار مقاس واحد على الأقل',
            'items.*.measurement_ids.array' => 'صيغة المقاسات غير صحيحة',
            'items.*.measurement_ids.*.required' => 'المقاس مطلوب',
            'items.*.measurement_ids.*.exists' => 'المقاس المحدد غير موجود',

            // Design Options
            'items.*.design_option_ids.array' => 'صيغة خيارات التصميم غير صحيحة',
            'items.*.design_option_ids.*.required' => 'خيار التصميم مطلوب',
            'items.*.design_option_ids.*.exists' => 'خيار التصميم المحدد غير موجود',
        ] : [
            // Address
            'address_id.required' => 'Please select a delivery address',
            'address_id.exists' => 'The selected address does not exist',

            // Notes
            'notes.max' => 'Notes cannot exceed 1000 characters',

            // Payment Method
            'payment_method.required' => 'Please select a payment method',
            'payment_method.in' => 'Invalid payment method',

            // Items
            'items.required' => 'At least one item must be added to the order',
            'items.min' => 'At least one item must be added to the order',
            'items.array' => 'Invalid items format',

            // Design ID
            'items.*.design_id.required' => 'Please select a design',
            'items.*.design_id.exists' => 'The selected design does not exist',

            // Quantity
            'items.*.quantity.required' => 'Please specify the quantity',
            'items.*.quantity.integer' => 'Quantity must be a valid number',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.quantity.max' => 'Quantity cannot exceed 100',

            // Measurements
            'items.*.measurement_ids.required' => 'Please select at least one size',
            'items.*.measurement_ids.min' => 'Please select at least one size',
            'items.*.measurement_ids.array' => 'Invalid measurements format',
            'items.*.measurement_ids.*.required' => 'Measurement is required',
            'items.*.measurement_ids.*.exists' => 'The selected measurement does not exist',

            // Design Options
            'items.*.design_option_ids.array' => 'Invalid design options format',
            'items.*.design_option_ids.*.required' => 'Design option is required',
            'items.*.design_option_ids.*.exists' => 'The selected design option does not exist',
        ];
    }

    /**
     * Custom validation after basic rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $locale = app()->getLocale();

            // Validate that address belongs to authenticated user
            if ($this->address_id) {
                $address = Address::find($this->address_id);
                if ($address && $address->user_id !== auth()->id()) {
                    $message = $locale === 'ar'
                        ? 'العنوان المحدد لا ينتمي لك'
                        : 'The selected address does not belong to you';
                    $validator->errors()->add('address_id', $message);
                }
            }

            // Validate items
            if ($this->items) {
                foreach ($this->items as $index => $item) {

                    // Check if design is active
                    if (isset($item['design_id'])) {
                        $design = \App\Models\Design::find($item['design_id']);
                        if ($design && !$design->is_active) {
                            $message = $locale === 'ar'
                                ? 'التصميم المحدد غير متاح حالياً'
                                : 'The selected design is not available';
                            $validator->errors()->add("items.{$index}.design_id", $message);
                        }
                    }

                    // Check for duplicate measurement_ids within same item
                    if (isset($item['measurement_ids'])) {
                        $measurementIds = $item['measurement_ids'];
                        if (count($measurementIds) !== count(array_unique($measurementIds))) {
                            $message = $locale === 'ar'
                                ? 'لا يمكن تكرار نفس المقاس في نفس العنصر'
                                : 'Cannot select the same size twice in one item';
                            $validator->errors()->add("items.{$index}.measurement_ids", $message);
                        }
                    }

                    // Check for duplicate design_option_ids within same item
                    if (isset($item['design_option_ids'])) {
                        $designOptionIds = $item['design_option_ids'];
                        if (count($designOptionIds) !== count(array_unique($designOptionIds))) {
                            $message = $locale === 'ar'
                                ? 'لا يمكن تكرار نفس خيار التصميم في نفس العنصر'
                                : 'Cannot select the same design option twice in one item';
                            $validator->errors()->add("items.{$index}.design_option_ids", $message);
                        }

                        // Check if all design options are active
                        foreach ($designOptionIds as $optionIndex => $optionId) {
                            $option = \App\Models\DesignOption::find($optionId);
                            if ($option && !$option->is_active) {
                                $message = $locale === 'ar'
                                    ? 'خيار التصميم المحدد غير متاح حالياً'
                                    : 'The selected design option is not available';
                                $validator->errors()->add(
                                    "items.{$index}.design_option_ids.{$optionIndex}",
                                    $message
                                );
                            }
                        }
                    }
                }
            }
        });
    }
}
