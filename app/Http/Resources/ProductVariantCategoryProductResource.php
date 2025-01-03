<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantCategoryProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'category_id' => $this->category_id, // Add this line
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'price' => $this->price,
            'discount' => $this->discount,
            'unit_id' => $this->unit_id,
            'unit_quantity' => $this->unit_quantity,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
            'product_title' => $this->product_title,
            'category_title' => $this->category_title,
        ];
    }
}
