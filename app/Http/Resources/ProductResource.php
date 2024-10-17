<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */////`bussiness_name`, `address`, `city`, `postal_code`, `phone_number`, `website`, `description`, `keywords`, `updated_at`, `created_at`, `is_approved`
     
     public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => 'required|exists:category,id',
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'priority' => 'nullable|integer',
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
