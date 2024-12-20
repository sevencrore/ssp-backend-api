<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionHistory;
use Illuminate\Http\Request;

class CommissionHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $commissions = CommissionHistory::all();
        return response()->json($commissions);
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
            'commission_type' => 'required|integer',
            'referal_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $commission = CommissionHistory::create($validated);
        return response()->json($commission, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  CommissionHistory  $commissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CommissionHistory $commissionHistory)
    {
        return response()->json($commissionHistory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CommissionHistory  $commissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CommissionHistory $commissionHistory)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'commission_type' => 'required|integer',
            'referal_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $commissionHistory->update($validated);
        return response()->json($commissionHistory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CommissionHistory  $commissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CommissionHistory $commissionHistory)
    {
        $commissionHistory->delete();
        return response()->json(null, 204);
    }
}
