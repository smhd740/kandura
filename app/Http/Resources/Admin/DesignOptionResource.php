<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignOptionResource extends JsonResource
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
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'image' => $this->image_url,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get type label
     */
    private function getTypeLabel(): array
    {
        $labels = [
            'color' => ['ar' => 'لون', 'en' => 'Color'],
            'fabric_type' => ['ar' => 'نوع القماش', 'en' => 'Fabric Type'],
            'sleeve_type' => ['ar' => 'نوع الكم', 'en' => 'Sleeve Type'],
            'dome_type' => ['ar' => 'نوع القبة', 'en' => 'Dome Type'],
        ];

        return $labels[$this->type] ?? ['ar' => $this->type, 'en' => $this->type];
    }
}
