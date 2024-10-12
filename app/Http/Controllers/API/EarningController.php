<?php

namespace App\Http\Controllers\API;

use App\Models\Earning;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class EarningController extends BaseController
{
    // Get all earnings with pagination
    public function index(Request $request): JsonResponse
    {
        $earnings = Earning::paginate($request->input('per_page', 10)); // Default to 10 items per page

        return response()->json([
            'success' => true,
            'message' => 'Earnings retrieved successfully.',
            'data' => $earnings->items(), // Return the items in the pagination
            'pagination' => [
                'total' => $earnings->total(),
                'current_page' => $earnings->currentPage(),
                'last_page' => $earnings->lastPage(),
                'per_page' => $earnings->perPage(),
            ],
        ]);
    }

    // Store a new earning
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'referral_incentive' => 'required|integer',
            'sale_value_estimated' => 'required|integer',
            'sale_actual_value' => 'required|integer',
            'wallet_amount' => 'required|integer',
            'self_purchase_total' => 'required|integer',
            'first_referral_purchase_total' => 'required|integer',
            'second_referral_purchase_total' => 'required|integer',
        ]);

        $earning = Earning::create($validatedData); // Create a new earning

        return response()->json([
            'success' => true,
            'message' => 'Earning created successfully.',
            'data' => $earning
        ], 201);
    }

    // Get a specific earning by ID
    public function show($id): JsonResponse
    {
        $earning = Earning::find($id);

        if (!$earning) {
            return response()->json([
                'success' => false,
                'message' => 'Earning not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Earning retrieved successfully.',
            'data' => $earning
        ]);
    }

    // Update a specific earning
    public function update(Request $request, $id): JsonResponse
    {
        $earning = Earning::find($id);

        if (!$earning) {
            return response()->json([
                'success' => false,
                'message' => 'Earning not found.',
            ], 404);
        }

        $validatedData = $request->validate([
            'referral_incentive' => 'required|integer',
            'sale_value_estimated' => 'required|integer',
            'sale_actual_value' => 'required|integer',
            'wallet_amount' => 'required|integer',
            'self_purchase_total' => 'required|integer',
            'first_referral_purchase_total' => 'required|integer',
            'second_referral_purchase_total' => 'required|integer',
        ]);

        $earning->update($validatedData); // Update the earning

        return response()->json([
            'success' => true,
            'message' => 'Earning updated successfully.',
            'data' => $earning
        ]);
    }

    // Delete a specific earning
    public function destroy($id): JsonResponse
    {
        $earning = Earning::find($id);

        if (!$earning) {
            return response()->json([
                'success' => false,
                'message' => 'Earning not found.',
            ], 404);
        }

        $earning->delete(); // Delete the earning

        return response()->json([
            'success' => true,
            'message' => 'Earning deleted successfully.',
        ]);
    }
}
