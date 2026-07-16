<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Full payload for details view.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->id, // UUID primary key
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'model' => new CarModelResource($this->whenLoaded('carModel')),
            'owner' => $this->whenLoaded('owner', fn () => ['name' => $this->owner->name]),
            'year' => $this->year,
            'price' => $this->price,
            'min_installment' => $this->min_installment,
            'mileage' => $this->mileage,
            'condition' => $this->condition,
            'transmission' => $this->transmission,
            'fuel_type' => $this->fuel_type,
            'grade' => $this->grade,
            'color' => $this->color,
            'status' => $this->status,
            'featured' => $this->featured,
            'views' => $this->view_count,
            'gallery' => $this->getGallery(),
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Map media items to gallery array.
     */
    private function getGallery(): array
    {
        if (!$this->relationLoaded('media')) {
            return [];
        }

        return $this->getMedia('car_gallery')->map(function ($media) {
            return [
                'uuid' => $media->uuid,
                'thumb' => $media->getUrl('thumb'),
                'medium' => $media->getUrl('medium'),
                'large' => $media->getUrl('large'),
                'original' => $media->getUrl(),
                'order' => $media->order_column,
            ];
        })->toArray();
    }
}
