<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EarningResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // This is the primary key, assuming you have it in your table
            'user_id' => $this->user_id,
            'referral_id' => $this->referral_id,
            'sale_id' => $this->sale_id,
            'referral_incentive' => $this->referral_incentive,
            'sale_value_estimated' => $this->sale_value_estimated,
            'sale_actual_value' => $this->sale_actual_value,
            'wallet_amount' => $this->wallet_amount,
            'self_purchase_total' => $this->self_purchase_total,
            'first_referral_purchase_total' => $this->first_referral_purchase_total,
            'second_referral_purchase_total' => $this->second_referral_purchase_total,
            'created_at' => $this->created_at->format('d/m/Y'), // Format date
            'updated_at' => $this->updated_at->format('d/m/Y'), // Format date
        ];
    }
}
