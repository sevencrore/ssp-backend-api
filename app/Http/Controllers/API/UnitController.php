<?php

namespace App\Http\Controllers\API;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Resources\UnitResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class UnitController extends BaseController
{
    // Get all Units
    public function index()
    {
        return Unit::all();
    }

    // Get paginated Units with sorting
    public function getAllPaginated(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort', 'title');
        $currentPage = $request->input('current_page', 1);

        $items = Unit::orderBy($sortField)
            ->paginate($perPage, ['*'], 'page', $currentPage)
            ->appends(['sort' => $sortField, 'current_page' => $currentPage]);

        $data = [
            'data' => UnitResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'next_page_url' => $items->nextPageUrl(),
                'prev_page_url' => $items->previousPageUrl(),
            ]
        ];

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    // Create a new Unit
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $unit = Unit::create($validatedData);

        return response()->json(['success' => true, 'data' => $unit], 201);
    }

    // Get a single Unit by id
    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json(['success' => true, 'data' => $unit], 200);
    }

    // Update a Unit
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update($validatedData);

        return response()->json(['success' => true, 'data' => $unit], 200);
    }

    // Delete a Unit
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully'
        ], 200);
    }
}
