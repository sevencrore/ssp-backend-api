<?php

namespace App\Http\Controllers;

use App\Models\Comission;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ComissionController extends Controller
{
    // Show all records
    public function index()
    {
        try {
            $comissions = Comission::all();
            return response()->json([
                'success' => true,
                'message' => 'Comissions fetched successfully',
                'data' => $comissions,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching comissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comissions',
            ], 500);
        }
    }

   

public function getAllPaginated(Request $request)
{
    try {
        $perPage = $request->get('per_page', 10); // Default 10 per page

        // Optional: search logic (if needed in future)
        $query = Comission::orderBy('created_at', 'desc');

        $queryParams = Arr::except($request->query(), []);

        $paginated = $query->paginate($perPage)->appends($queryParams);

        return response()->json([
            'success' => true,
            'message' => 'Commissions fetched successfully',
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'next_page_url' => $paginated->nextPageUrl(),
                'prev_page_url' => $paginated->previousPageUrl(),
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching commissions: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch commissions',
        ], 500);
    }
}

    // Show a single record
    public function show($id)
    {
        try {
            $comission = Comission::find($id);
            if ($comission) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comission fetched successfully',
                    'data' => $comission,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Comission not found',
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching comission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comission',
            ], 500);
        }
    }

    // Store a new record
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'minimum_order' => 'required|numeric',
            ]);

            $comission = Comission::create($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Comission created successfully',
                'data' => $comission,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating comission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comission',
            ], 500);
        }
    }

    // Update an existing record
    public function update(Request $request, $id)
    {
        try {
            $comission = Comission::find($id);

            if ($comission) {
                $validatedData = $request->validate([
                    'minimum_order' => 'required|numeric',
                ]);

                $comission->update($validatedData);
                return response()->json([
                    'success' => true,
                    'message' => 'Comission updated successfully',
                    'data' => $comission,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Comission not found',
                ], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating comission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comission',
            ], 500);
        }
    }

    // Delete a record
    public function destroy($id)
    {
        try {
            $comission = Comission::find($id);
            if ($comission) {
                $comission->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Comission deleted successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Comission not found',
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting comission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comission',
            ], 500);
        }
    }

    // Get minimum order by user_id
    public function getMinimumOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
            ]);

            $userDetail = UserDetails::where('user_id', $validated['user_id'])->first();

            if (!$userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User details not found.',
                ], 404);
            }

            $comission = Comission::find($userDetail->comission_id);

            if (!$comission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commission details not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Minimum order fetched successfully',
                'minimum_order' => $comission->minimum_order,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error fetching minimum order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch minimum order',
            ], 500);
        }
    }
}
