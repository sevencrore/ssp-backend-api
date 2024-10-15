<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UnitResource",
 *     type="object",
 *     title="UnitResource",
 *     description="A resource representing a unit",
 *     @OA\Property(property="id", type="integer", description="Unit ID"),
 *     @OA\Property(property="title", type="string", description="Unit title"),
 *     @OA\Property(property="description", type="string", description="Unit description"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation date"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Update date")
 * )
 */

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
