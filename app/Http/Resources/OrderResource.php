<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderItemResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=10),
 *     @OA\Property(property="product_id", type="integer", example=20),
 *     @OA\Property(property="user_id", type="integer", example=123),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="integer", example=50),
 *     @OA\Property(property="total_amount", type="integer", example=100), // Added total_amount
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */
class OrderResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_amount' => $this->total_amount, // Added total_amount
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
