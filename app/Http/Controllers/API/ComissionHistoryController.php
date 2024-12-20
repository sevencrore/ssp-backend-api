<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ComissionHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
