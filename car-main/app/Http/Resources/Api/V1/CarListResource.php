<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Optimized for search/list endpoints.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->id, // UUID primary key
            'slug' => $this->slug,
            'title' => $this->title,
            'brand' => $this->whenLoaded('brand', fn () => $this->brand->name),
            'model' => $this->whenLoaded('carModel', fn () => $this->carModel->name),
            'year' => $this->year,
            'price' => $this->price,
            'condition' => $this->condition,
            'transmission' => $this->transmission,
            'thumbnail' => $this->getFirstMediaUrl('car_gallery', 'thumb') ?: null,
            'featured' => $this->featured,
            'views' => $this->view_count,
        ];
    }
}
