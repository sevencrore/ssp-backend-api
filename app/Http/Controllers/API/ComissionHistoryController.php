<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ComissionHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ComissionHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comissions = ComissionHistory::all();
        return response()->json($comissions);
    }



    public function addCommissionRecord($user_id, $comission_type, $referal_id, $amount, $description)
    {
        try {

            Log::info("$user_id and the $amount in the add comissionrecord");

            if ($user_id == $referal_id){
                $comission_type=2;
            }
            // Create a new ComissionHistory record
            $comissionHistory = ComissionHistory::create([
                'user_id' => $user_id,
                'comission_type' => $comission_type,
                'referal_id' => $referal_id,
                'amount' => $amount,
                'description' => $description,
            ]);

            Log::info("$user_id and the $amount in the add comissionrecord but not savaing");

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Commission record added successfully!',
                'data' => $comissionHistory,
            ], 201);
        } catch (\Exception $e) {
            // Handle exception and return error response
            Log::info("$e");
            return response()->json([
                'success' => false,
                'message' => 'Failed to add commission record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comission_type' => 'required|integer',
            'referal_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $comission = ComissionHistory::create($validated);
        return response()->json($comission, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  ComissionHistory  $comissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ComissionHistory $comissionHistory)
    {
        return response()->json($comissionHistory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ComissionHistory  $comissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ComissionHistory $comissionHistory)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comission_type' => 'nullable|integer',
            'referal_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $comissionHistory->update($validated);
        return response()->json($comissionHistory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ComissionHistory  $comissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ComissionHistory $comissionHistory)
    {
        $comissionHistory->delete();
        return response()->json(null, 204);
    }

    /**
     * Get commission histories by user_id.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByUserId($userId)
    {
        $comissions = ComissionHistory::where('user_id', $userId)->get();
        return response()->json($comissions);
    }
}
