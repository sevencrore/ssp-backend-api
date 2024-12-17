<?php

namespace App\Http\Controllers;

use App\Models\Comission;
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
}