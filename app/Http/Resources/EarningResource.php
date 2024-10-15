<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="EarningResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="referral_incentive", type="integer", example=100),
 *     @OA\Property(property="sale_value_estimated", type="integer", example=500),
 *     @OA\Property(property="sale_actual_value", type="integer", example=450),
 *     @OA\Property(property="wallet_amount", type="integer", example=50),
 *     @OA\Property(property="self_purchase_total", type="integer", example=200),
 *     @OA\Property(property="first_referral_purchase_total", type="integer", example=150),
 *     @OA\Property(property="second_referral_purchase_total", type="integer", example=100),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */

class EarningResource extends JsonResource
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
            'referral_incentive' => $this->referral_incentive,
            'sale_value_estimated' => $this->sale_value_estimated,
            'sale_actual_value' => $this->sale_actual_value,
            'wallet_amount' => $this->wallet_amount,
            'self_purchase_total' => $this->self_purchase_total,
            'first_referral_purchase_total' => $this->first_referral_purchase_total,
            'second_referral_purchase_total' => $this->second_referral_purchase_total,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'user_id'=>$this->user_id,
        ];
    }
}
