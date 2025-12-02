<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
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
            'name' => [
                'ar' => $this->getTranslation('name', 'ar'),
                'en' => $this->getTranslation('name', 'en'),
            ],
            'description' => [
                'ar' => $this->getTranslation('description', 'ar'),
                'en' => $this->getTranslation('description', 'en'),
            ],
            'price' => (float) $this->price,
            'is_active' => $this->is_active,

            // User info
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],

            // Measurement/Size
            'measurement' => [
                'id' => $this->measurement->id,
                'size' => $this->measurement->size,
            ],

            // Images
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'is_primary' => $image->is_primary,
                    'order' => $image->order,
                ];
            }),

            'primary_image' => $this->primary_image_url,

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

