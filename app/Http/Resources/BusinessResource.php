<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="BusinessResource",
     *     type="object",
     *     title="BusinessResource",
     *     description="A resource representing a business",
     *     @OA\Property(property="id", type="integer", description="Business ID"),
     *     @OA\Property(property="business_name", type="string", description="Business Name"),
     *     @OA\Property(property="address", type="string", description="Business Address"),
     *     @OA\Property(property="city", type="string", description="City where the business is located"),
     *     @OA\Property(property="postal_code", type="string", description="Postal Code"),
     *     @OA\Property(property="phone_number", type="string", description="Contact Phone Number"),
     *     @OA\Property(property="website", type="string", description="Website URL"),
     *     @OA\Property(property="description", type="string", description="Business Description"),
     *     @OA\Property(property="keywords", type="string", description="Keywords related to the business"),
     *     @OA\Property(property="is_approved", type="boolean", description="Approval status of the business"),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation date"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Update date"),
     * )
     */


     public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'phone_number' => $this->phone_number,
            'website' => $this->website,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'is_approved' => $this->is_approved,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
