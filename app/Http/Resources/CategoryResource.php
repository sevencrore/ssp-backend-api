<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *     title="CategoryResource",
 *     description="A single category",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Category Title"),
 *     @OA\Property(property="description", type="string", example="Category Description"),
 *     @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-14T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-14T12:00:00Z")
 * )
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];
    }
}
