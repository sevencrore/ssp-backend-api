<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\UserDetails;
use Illuminate\Http\Request;

class ComissionController extends Controller
{
    // Show all records
    public function index()
    {
        $comissions = Comission::all();
        return response()->json($comissions);
    }

    // Show a single record
    public function show($id)
    {
        $comission = Comission::find($id);
        if ($comission) {
            return response()->json($comission);
        } else {
            return response()->json(['message' => 'Comission not found'], 404);
        }
    }

    // Store a new record
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'minimum_order' => 'required|numeric',
        ]);

        $comission = Comission::create($validatedData);
        return response()->json($comission, 201);
    }

    // Update an existing record
    public function update(Request $request, $id)
    {
        $comission = Comission::find($id);

        if ($comission) {
            $validatedData = $request->validate([
                'minimum_order' => 'required|numeric',
            ]);

            $comission->update($validatedData);
            return response()->json($comission);
        } else {
            return response()->json(['message' => 'Comission not found'], 404);
        }
    }

    // Delete a record
    public function destroy($id)
    {
        $comission = Comission::find($id);
        if ($comission) {
            $comission->delete();
            return response()->json(['message' => 'Comission deleted successfully']);
        } else {
            return response()->json(['message' => 'Comission not found'], 404);
        }
    }

    public function getMinimumOrder(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'user_id' => 'required|integer',
        ]);

        // Fetch user details
        $userDetail = UserDetails::where('user_id', $validated['user_id'])->first();

        if (!$userDetail) {
            return response()->json([
                'message' => 'User details not found.',
            ], 404);
        }

        // Get commission details
        $comission = Comission::find($userDetail->comission_id);

        if (!$comission) {
            return response()->json([
                'message' => 'Commission details not found.',
            ], 404);
        }

        // Return the minimum order
        return response()->json([
            'minimum_order' => $comission->minimum_order,
        ], 200);
    }


}
