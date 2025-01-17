<?php

namespace App\Http\Controllers;

use App\Models\ComissionDetail;
use App\Models\User;
use Illuminate\Http\Request;

class ComissionDetailController extends Controller
{
    // Get all commission details
    public function index()
    {
        $details = ComissionDetail::with('comission')->get();
        return response()->json($details);
    }

    // Get a specific commission detail
    public function show($id)
    {
        $detail = ComissionDetail::with('comission')->find($id);

        if ($detail) {
            return response()->json($detail);
        } else {
            return response()->json(['message' => 'Comission detail not found'], 404);
        }
    }

    // Store a new commission detail
    public function store(Request $request)
    {  
        $validatedData = $request->validate([
            'comission_id' => 'required|exists:comission,id',
            'level' => 'required|integer',
            'commission' => 'required|numeric',
        ]);
        $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }

        $detail = ComissionDetail::create($validatedData);
        return response()->json($detail, 201);
    }

    // Update an existing commission detail
    public function update(Request $request, $id)
    {  
          $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }

        $detail = ComissionDetail::find($id);

        if ($detail) {
            $validatedData = $request->validate([
                'comission_id' => 'required|exists:comission,id',
                'level' => 'required|integer',
                'commission' => 'required|numeric',
            ]);

            $detail->update($validatedData);
            return response()->json($detail);
        } else {
            return response()->json(['message' => 'Comission detail not found'], 404);
        }
    }

    // Delete a commission detail
    public function destroy(Request $request,$id)
    {    $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }

        $detail = ComissionDetail::find($id);

        if ($detail) {
            $detail->delete();
            return response()->json(['message' => 'Comission detail deleted successfully']);
        } else {
            return response()->json(['message' => 'Comission detail not found'], 404);
        }
    }
}
