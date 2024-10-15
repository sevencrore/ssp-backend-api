<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResource extends JsonResource
{

    
/**
 * @OA\Schema(
 *     schema="UserDetailsResource",
 *     type="object",
 *     title="UserDetailsResource",
 *     description="A resource representing a userDetails",
 *     @OA\Property(property="id", type="integer", description="UserDetails ID"),
 *     @OA\Property(property="first_name", type="string", description="User name"),
 *     @OA\Property(property="middle_name", type="string", description="User middle name"),
 *     @OA\Property(property="last_name", type="string", description="User last name"),
 *     @OA\Property(property="phone_1", type="string", description="Contact Phone Number1"),
 *     @OA\Property(property="phone_2", type="string", description="Contact Phone Number2"),
 *     @OA\Property(property="email", type="string", email="User email"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation date"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Update date")
 * )
 */
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'phone_1' => $this->phone_1,
            'phone_2' => $this->phone_2,
            'email' => $this->email,
            'user_id' => $this->user_id,
            'aadhar_number' => $this->aadhar_number,
            'referral_code' => $this->referral_code,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
