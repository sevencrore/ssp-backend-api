<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    // Store a new address
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'city_id' => 'required|integer',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:15',
            'user_id' => 'required|integer',
        ]);

        $address = Address::create($validatedData); // Create a new address

        return response()->json(['success' => true, 'data' => $address], 201);
    }

    // Add other methods (index, show, update, destroy) as needed
}
