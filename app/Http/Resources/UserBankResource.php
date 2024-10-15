<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @OA\Schema(
     *     schema="UserBankResource",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="bank_name", type="string"),
     *     @OA\Property(property="account_number", type="string"),
     *     @OA\Property(property="ifsc_code", type="string"),
     *     @OA\Property(property="branch_name", type="string"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'ifsc_code' => $this->ifsc_code,
            'branch_name' => $this->branch_name,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
