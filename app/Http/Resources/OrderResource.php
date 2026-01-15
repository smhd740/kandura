<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'total_amount' => (float) $this->total_amount,
            'notes' => $this->notes,

            'payment_method' => $this->payment_method,
        'payment_status' => $this->payment_status,
        'stripe_payment_intent_id' => $this->stripe_payment_intent_id,
        'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            // User info (basic)
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],

            // Address info
            'address' => [
                'id' => $this->address->id,
                'street' => $this->address->street,
                'building' => $this->address->building,
                'floor' => $this->address->floor,
                'apartment' => $this->address->apartment,
                'city' => [
                    'id' => $this->address->city->id,
                    'name' => $this->address->city->name,
                ],
            ],

            // Order Items
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,

                    // Design info
                    'design' => [
                        'id' => $item->design->id,
                        'name' => [
                            'ar' => $item->design->getTranslation('name', 'ar'),
                            'en' => $item->design->getTranslation('name', 'en'),
                        ],
                        'description' => [
                            'ar' => $item->design->getTranslation('description', 'ar'),
                            'en' => $item->design->getTranslation('description', 'en'),
                        ],
                        'primary_image' => $item->design->primary_image_url,
                    ],

                    // Measurements
                    'measurements' => $item->measurements->map(function ($measurement) {
                        return [
                            'id' => $measurement->id,
                            'size' => $measurement->size,
                        ];
                    }),

                    // Design Options
                    'design_options' => $item->designOptions->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => [
                                'ar' => $option->getTranslation('name', 'ar'),
                                'en' => $option->getTranslation('name', 'en'),
                            ],
                            'type' => $option->type,
                        ];
                    }),
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
