<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\UserDetails;
Use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        try {
            $validatedData = $request->validate([
                'first_name'     => 'nullable|string|max:255',
                'last_name'      => 'nullable|string|max:255',
                'district_name'  => 'required|string|max:255',
                'city_name'      => 'required|string|max:255',
                'city_id'        => 'nullable|integer',
                'address'        => 'required|string',
                'pin_code'       => 'required|string|max:10',
                'phone_number'   => 'required|string|max:15',
                'user_id'        => 'required|exists:users,id',
                'latitude'       => 'nullable|numeric',
                'longitude'      => 'nullable|numeric',
            ]);

            $validatedData['user_id'] = $request->user_id;

            $address = Address::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $address
            ], 201); // 201 = Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create address',
                'error' => $e->getMessage()
            ], 500); // 500 = Internal Server Error
        }
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
        try {
            $address = Address::findOrFail($id);

            if ($address->user_id != $request->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: failed to update the address',
                ], 403); // 403 = Forbidden
            }

            $validatedData = $request->validate([
                'first_name'     => 'nullable|string|max:255',
                'last_name'      => 'nullable|string|max:255',
                'district_name'  => 'required|string|max:255',
                'city_name'      => 'required|string|max:255',
                'city_id'        => 'nullable|integer',
                'address'        => 'required|string',
                'pin_code'       => 'required|string|max:10',
                'phone_number'   => 'required|string|max:15',
                'latitude'       => 'nullable|numeric',
                'longitude'      => 'nullable|numeric',
            ]);

            $address->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address',
                'error' => $e->getMessage()
            ], 500);
        }
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
            Log::info("User ID: $request->user_id");

            // Get the first address for the given user
            $address = Address::where('user_id', $request->user_id)->first();

            // Check if address exists
            if ($address) {
                Log::info($address);
                return response()->json([
                    'success' => true,
                    'message' => 'Address found',
                    'data' => $address
                ], 200); // Status 200 for success
            } else {
                $userDetails = UserDetails::where('user_id', $request->user_id)->first();
                return response()->json([
                    'success' => false,
                    'message' => 'No address found for this user',
                    'pincode' => $userDetails->pincode ?? null,
                ], 404); // Status 404 is more appropriate here
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving address: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the address'
            ], 500); // Status 500 on exception
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
    public function getUserAddressByEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // First, find the user by email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for the provided email',
                ], 404);
            }

            // Get the address linked to the user
            $address = Address::where('user_id', $user->id)->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found for this user',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Address retrieved successfully',
                'data' => $address,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error retrieving user address by email: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An internal error occurred while retrieving the address',
            ], 500);
        }
    }
}
