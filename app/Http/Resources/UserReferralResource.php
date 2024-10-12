<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReferralResource extends JsonResource
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
            'id' => $this->id,
            'reg_user_id' => $this->reg_user_id,
            'referral_id' => $this->referral_id,
            'created_at' => $this->created_at->format('d/m/Y'), // Format date
            'updated_at' => $this->updated_at->format('d/m/Y'), // Format date
        ];
    }
}
