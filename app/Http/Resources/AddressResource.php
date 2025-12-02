<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => [
                'id' => $this->city->id,
                'name' => $this->city->name,
            ],
            'street' => $this->street,
            'building_number' => $this->building_number,
            'house_number' => $this->house_number,
            'details' => $this->details,
            'coordinates' => $this->coordinates,
            'is_default' => $this->is_default,
            'full_address' => $this->full_address,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
