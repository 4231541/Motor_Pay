<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'logo' => [
                'thumb' => $this->getFirstMediaUrl('brand_logo', 'thumb') ?: null,
                'medium' => $this->getFirstMediaUrl('brand_logo', 'medium') ?: null,
                'original' => $this->getFirstMediaUrl('brand_logo') ?: null,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
