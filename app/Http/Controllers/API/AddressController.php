<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AddressController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::all();
        return response()->json($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        $validatedData['user_id'] = $request->user_id;

        $address = Address::create($validatedData);
        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $address = Address::findOrFail($id);
        return response()->json($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        if($address->user_id != $request->user_id){
            return response()->json([
                'success' => false,
                'message' => 'failed to update the adress',
            ], 404);
        }
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:15',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address->update($validatedData);
        return response()->json($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * Get all addresses by a specific user ID.
     */
    public function getAddressesByUserId(Request $request)
    {
        $addresses = Address::where('user_id', $request->user_id)->first();
        return response()->json($addresses);
    }

    public function getUserAddressesById(Request $request)
    {
        $addresses = Address::where('user_id', $request->user_id)->first();
        return response()->json($addresses);
    }
    public function GetUserAddresses(Request $request)
    {
        try {
            Log::info("$request->user_id");
            
            // Attempt to get the first address for the given user
            $addresses = Address::where('user_id', $request->user_id)->first();
            
            // Check if addresses were found
            if ($addresses) {
                Log::info($addresses);
                return response()->json([
                    'success' => true,
                    'message' => 'Address found',
                    'data' => $addresses
                ]);
            } else {
                // If no address found, return a no address found message
                return response()->json([
                    'success' => false,
                    'message' => 'No address found for this user'
                ]);
            }
        } catch (\Exception $e) {
            // Log the error and return a response indicating failure
            Log::error('Error retrieving address: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the address'
            ]);
        }
    }

    public function getUserAddressByUserID($userID)
    {
        try {
            $address = Address::where('user_id', $userID)->first();
    
            return [
                'success' => true,
                'address' => $address,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch address.',
                'error' => $e->getMessage(),
            ];
        }
    }
    
}
