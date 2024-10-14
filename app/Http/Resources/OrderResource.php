<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=123),
 *     @OA\Property(property="address_id", type="integer", example=456),
 *     @OA\Property(property="order_status", type="integer", example=1),
 *     @OA\Property(property="tracking_number", type="string", example="TRACK12345"),
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
            'user_id' => $this->user_id,
            'address_id' => $this->address_id,
            'order_status' => $this->order_status,
            'tracking_number' => $this->tracking_number,
            'total_amount' => $this->total_amount, // Added total_amount
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
