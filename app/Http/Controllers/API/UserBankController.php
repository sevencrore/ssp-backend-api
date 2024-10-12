<?php

namespace App\Http\Controllers\API;

use App\Models\UserBank;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class UserBankController extends BaseController
{
    // Get all user banks with pagination
    public function index(Request $request): JsonResponse
    {
        $userBanks = UserBank::paginate($request->input('per_page', 10)); // Default to 10 items per page

        return response()->json([
            'success' => true,
            'message' => 'User banks retrieved successfully.',
            'data' => $userBanks->items(), // Return the items in the pagination
            'pagination' => [
                'total' => $userBanks->total(),
                'current_page' => $userBanks->currentPage(),
                'last_page' => $userBanks->lastPage(),
                'per_page' => $userBanks->perPage(),
            ],
        ]);
    }

    // Store a new user bank
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string|unique:user_bank',
            'ifsc_code' => 'required|string',
            'branch_name' => 'required|string',
        ]);

        $userBank = UserBank::create($validatedData); // Create a new user bank

        return response()->json([
            'success' => true,
            'message' => 'User bank created successfully.',
            'data' => $userBank,
        ], 201);
    }

    // Get a specific user bank by ID
    public function show($id): JsonResponse
    {
        $userBank = UserBank::find($id);

        if (!$userBank) {
            return response()->json([
                'success' => false,
                'message' => 'User bank not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User bank retrieved successfully.',
            'data' => $userBank,
        ]);
    }

    // Update a specific user bank
    public function update(Request $request, $id): JsonResponse
    {
        $userBank = UserBank::find($id);

        if (!$userBank) {
            return response()->json([
                'success' => false,
                'message' => 'User bank not found.',
            ], 404);
        }

        $validatedData = $request->validate([
            'bank_name' => 'sometimes|required|string',
            'account_number' => 'sometimes|required|string|unique:user_bank,account_number,' . $userBank->id,
            'ifsc_code' => 'sometimes|required|string',
            'branch_name' => 'sometimes|required|string',
        ]);

        $userBank->update($validatedData); // Update the user bank

        return response()->json([
            'success' => true,
            'message' => 'User bank updated successfully.',
            'data' => $userBank,
        ]);
    }

    // Delete a specific user bank
    public function destroy($id): JsonResponse
    {
        $userBank = UserBank::find($id);

        if (!$userBank) {
            return response()->json([
                'success' => false,
                'message' => 'User bank not found.',
            ], 404);
        }

        $userBank->delete(); // Delete the user bank

        return response()->json([
            'success' => true,
            'message' => 'User bank deleted successfully.',
        ]);
    }
}
