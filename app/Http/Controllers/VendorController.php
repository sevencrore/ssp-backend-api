<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


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
    public function getAllPaginated(Request $request): JsonResponse
    {
        try {
            // Get search query and pagination size
            $search = $request->query('search');
            $perPage = $request->query('per_page', 10); // default 10 per page

            // Query builder
            $query = Vendor::query();

            // Apply search filters
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('middle_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('business_name', 'LIKE', "%$search%");
                });
            }

            // Paginate and append search query
            $vendors = $query->paginate($perPage)->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

            return response()->json([
                'success' => true,
                'vendors' => $vendors->items(), // return just the items
                'total' => $vendors->total(),
                'current_page' => $vendors->currentPage(),
                'per_page' => $vendors->perPage(),
                'last_page' => $vendors->lastPage(),
                'has_next_page' => $vendors->hasMorePages(),
                'has_previous_page' => $vendors->currentPage() > 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve vendors',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
