<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\MeasurementResource;
use App\Http\Resources\Admin\DesignOptionResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'is_active' => (bool) $this->is_active,

            // Images
            'images' => DesignImageResource::collection($this->whenLoaded('images')),
            'primary_image' => new DesignImageResource($this->whenLoaded('images', function() {
                return $this->images->where('is_primary', true)->first();
            })),

            // Measurements (sizes)
            'measurements' => MeasurementResource::collection($this->whenLoaded('measurements')),

            // Design Options
            'design_options' => DesignOptionResource::collection($this->whenLoaded('designOptions')),

            // User (owner)
            'user' => new UserResource($this->whenLoaded('user')),

            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
