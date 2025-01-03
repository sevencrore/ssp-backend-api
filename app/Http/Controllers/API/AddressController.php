<?php

namespace App\Http\Controllers\API;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class AddressController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::all();
        return response()->json($addresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:15',
            'user_id' => 'required|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        $validatedData['user_id'] = $request->user_id;

        $address = Address::create($validatedData);
        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $address = Address::findOrFail($id);
        return response()->json($address);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer',
            'address' => 'required|string',
            'pin_code' => 'required|string|max:10',
            'phone_number' => 'required|string|max:15',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address->update($validatedData);
        return response()->json($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * Get all addresses by a specific user ID.
     */
    public function getAddressesByUserId($userId)
    {
        $addresses = Address::where('user_id', $userId)->get();
        return response()->json($addresses);
    }
}
