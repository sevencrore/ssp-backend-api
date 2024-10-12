<?php

namespace App\Http\Controllers\API;

use App\Models\UserReferral;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class UserReferralController extends BaseController
{
    // Get all user referrals
    public function index(): JsonResponse
    {
        $userReferrals = UserReferral::all(); // Fetch all user referrals

        return response()->json(['success' => true, 'data' => $userReferrals]);
    }

    // Store a new user referral
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'reg_user_id' => 'required|integer', // Changed to integer
            'referral_id' => 'required|integer', // Changed to integer
        ]);

        $userReferral = UserReferral::create($validatedData); // Create a new user referral

        return response()->json(['success' => true, 'data' => $userReferral], 201);
    }

    // Get a specific user referral by ID
    public function show(int $id): JsonResponse // Enforce ID as an integer
    {
        $userReferral = UserReferral::find($id);

        if (!$userReferral) {
            return response()->json(['success' => false, 'message' => 'User referral not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $userReferral]);
    }

    // Update a specific user referral
    public function update(Request $request, int $id): JsonResponse // Enforce ID as an integer
    {
        $userReferral = UserReferral::find($id);

        if (!$userReferral) {
            return response()->json(['success' => false, 'message' => 'User referral not found'], 404);
        }

        $validatedData = $request->validate([
            'reg_user_id' => 'sometimes|required|integer', // Changed to integer
            'referral_id' => 'sometimes|required|integer', // Changed to integer
        ]);

        $userReferral->update($validatedData); // Update the user referral

        return response()->json(['success' => true, 'data' => $userReferral]);
    }

    // Delete a specific user referral
    public function destroy(int $id): JsonResponse // Enforce ID as an integer
    {
        $userReferral = UserReferral::find($id);

        if (!$userReferral) {
            return response()->json(['success' => false, 'message' => 'User referral not found'], 404);
        }

        $userReferral->delete(); // Delete the user referral

        return response()->json(['success' => true, 'message' => 'User referral deleted successfully']);
    }
}
