<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */////`user_id`, `product_id`,`updated_at`, `created_at`, `is_approved`

     /**
     * @OA\Schema(
     *     schema="CartResource",
     *     type="object",
     *     title="CartResource",
     *     description="A single cart item",
     *     @OA\Property(property="id", type="integer", example=1, description="Cart item ID"),
     *     @OA\Property(property="user_id", type="integer", example=10, description="ID of the user who owns the cart item"),
     *     @OA\Property(property="product_id", type="integer", example=5, description="ID of the product in the cart"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-14T10:30:00Z", description="Timestamp when the item was created"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-15T12:00:00Z", description="Timestamp when the item was last updated")
     * )
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
