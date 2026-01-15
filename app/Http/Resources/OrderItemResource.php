<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'subtotal' => (float) $this->subtotal,

            // Design info
            'design' => [
                'id' => $this->design->id,
                'name' => [
                    'ar' => $this->design->getTranslation('name', 'ar'),
                    'en' => $this->design->getTranslation('name', 'en'),
                ],
                'primary_image' => $this->design->primary_image_url,
            ],

            // Measurements
            'measurements' => $this->measurements->map(function ($measurement) {
                return [
                    'id' => $measurement->id,
                    'size' => $measurement->size,
                ];
            }),

            // Design Options
            'design_options' => $this->designOptions->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => [
                        'ar' => $option->getTranslation('name', 'ar'),
                        'en' => $option->getTranslation('name', 'en'),
                    ],
                    'type' => $option->type,
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
