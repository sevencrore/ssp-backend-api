<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Http\JsonResponse;

class UserDetailsController extends Controller
{
    // Get all user details
    public function index(): JsonResponse
    {
        $userDetails = UserDetails::all(); // Fetch all user details

        return response()->json(['success' => true, 'data' => $userDetails]);
    }

    // Store a new user detail
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_1' => 'required|string|max:15',
            'phone_2' => 'nullable|string|max:15',
            'email' => 'required|string|email|max:255|unique:user_details,email',
            'user_id'=>'required|integer',
            'aadhar_number'=>'required|integer',
            'referral_code'=>'required|string',
        ]);

        $userDetail = UserDetails::create($validatedData); // Create a new user detail

        return response()->json(['success' => true, 'data' => $userDetail], 201);
    }

    // Get a specific user detail by ID
    public function show($id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $userDetail]);
    }

    // Update a specific user detail
    public function update(Request $request, $id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        $validatedData = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone_1' => 'sometimes|required|string|max:15',
            'phone_2' => 'sometimes|nullable|string|max:15',
            'email' => 'sometimes|required|string|email|max:255|unique:user_details,email,' . $userDetail->id,
            'user_id'=>'required|integer',
            'aadhar_number'=>'required|integer',
            'referral_code'=>'required|string',
        ]);

        $userDetail->update($validatedData); // Update the user detail

        return response()->json(['success' => true, 'data' => $userDetail]);
    }

    // Delete a specific user detail
    public function destroy($id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        $userDetail->delete(); // Delete the user detail

        return response()->json(['success' => true, 'message' => 'User detail deleted successfully']);
    }
}
