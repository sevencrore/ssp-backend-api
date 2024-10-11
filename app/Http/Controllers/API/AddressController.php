<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class AddressController extends BaseController
{
    // Get all addresses
    public function index(): JsonResponse
    {
        $addresses = Address::all(); // Fetch all addresses

        return response()->json(['success' => true, 'data' => $addresses]);
    }

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
            // 'latitude' => 'sometimes|required|decimal:11,8',
            // 'longitude' => 'sometimes|required|decimal:11,8',
        ]);

        $address = Address::create($validatedData); // Create a new address

        return response()->json(['success' => true, 'data' => $address], 201);
    }

    // Get all addresses with pagination
    public function getAllPaginated(Request $request): JsonResponse
    {
        $addresses = Address::paginate($request->input('per_page', 10)); // Default to 10 items per page

        return response()->json(['success' => true, 'data' => $addresses]);
    }

    // Get a specific address by ID
    public function show($address): JsonResponse
    {
        $address = Address::find($address);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $address]);
    }

    // Update a specific address
    public function update(Request $request, $address): JsonResponse
    {
        $address = Address::find($address);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        $validatedData = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'city_id' => 'sometimes|required|integer',
            'address' => 'sometimes|required|string',
            'pin_code' => 'sometimes|required|string|max:10',
            'phone_number' => 'sometimes|required|string|max:15',
            'user_id' => 'sometimes|required|integer',
            'latitude' => 'sometimes|required|decimal:8,6',  // Update validation for latitude
            'longitude' => 'sometimes|required|decimal:8,6', // Update validation for longitude
        ]);

        $address->update($validatedData); // Update the address

        return response()->json(['success' => true, 'data' => $address]);
    }

    // Delete a specific address
    public function destroy($address): JsonResponse
    {
        $address = Address::find($address);

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        $address->delete(); // Delete the address

        return response()->json(['success' => true, 'message' => 'Address deleted successfully']);
    }
}
