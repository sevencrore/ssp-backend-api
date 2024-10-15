<?php

namespace App\Http\Controllers\API;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Resources\UnitResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class UnitController extends BaseController
{
     /**
     * @OA\Get(
     *     path="/api/unit",
     *     tags={"Unit"},
     *     summary="Get all units",
     *     description="Returns a list of all units.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Unit Title"),
     *                     @OA\Property(property="description", type="string", example="Unit Description")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    // Get all Units
    public function index()
    {
        return Unit::all();
    }

    /**
     * @OA\Get(
     *     path="/api/units-get-all-paginated",
     *     tags={"Unit"},
     *     summary="Get paginated units",
     *     description="Returns a paginated list of units.",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page.",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Field to sort by.",
     *         @OA\Schema(type="string", example="title")
     *     ),
     *     @OA\Parameter(
     *         name="current_page",
     *         in="query",
     *         required=false,
     *         description="Current page number.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Unit Title"),
     *                     @OA\Property(property="description", type="string", example="Unit Description")
     *                 )),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer"),
     *                     @OA\Property(property="last_page", type="integer"),
     *                     @OA\Property(property="per_page", type="integer"),
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="next_page_url", type="string"),
     *                     @OA\Property(property="prev_page_url", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

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

    
    /**
     * @OA\Post(
     *     path="/api/unit",
     *     tags={"Unit"},
     *     summary="Create a new unit",
     *     description="Creates a new unit.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Category"),
     *             @OA\Property(property="description", type="string", example="Category Description")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="City created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Category"),
     *                 @OA\Property(property="description", type="string", example="Category Description")
     *             )
     * )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input.")
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/api/unit/{id}",
     *     tags={"Unit"},
     *     summary="Get a specific unit",
     *     description="Returns details of a specific unit.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Cityy Title"),
     *                 @OA\Property(property="description", type="string", example="City Description")     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found.")
     * )
     */

    // Get a single Unit by id
    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json(['success' => true, 'data' => $unit], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/unit/{id}",
     *     tags={"Unit"},
     *     summary="Update a unit",
     *     description="Updates an existing unit.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated City"),
     *             @OA\Property(property="description", type="string", example="Updated Description")     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unit updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Unit"),
     *                 @OA\Property(property="description", type="string", example="Updated Description")     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Unit not found.")
     * )
     */

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

     /**
     * @OA\Delete(
     *     path="/api/unit/{id}",
     *     tags={"Unit"},
     *     summary="Delete a unit",
     *     description="Deletes a specific unit.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=204,
     *         description="City deleted successfully."
     *     ),
     *     @OA\Response(response=404, description="Unit not found.")
     * )
     */

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
