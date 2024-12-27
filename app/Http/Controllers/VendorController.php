<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return response()->json($vendors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'business_name' => 'required|string',
            'phone_1' => 'required|digits:10',
            'aadhar_number' => 'required|string|size:12',
            'address' => 'required|string',
            'pincode' => 'required|digits:10',
            'longitude' => 'required|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        $vendor = Vendor::create($request->all());
        return response()->json($vendor, 201);
    }

    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json($vendor);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string',
            'business_name' => 'required|string',
            'phone_1' => 'required|digits:10',
            'aadhar_number' => 'required|string|size:12',
            'address' => 'required|string',
            'pincode' => 'required|digits:10',
            'longitude' => 'required|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        $vendor->update($request->all());
        return response()->json($vendor);
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return response()->json(null, 204);
    }
}
