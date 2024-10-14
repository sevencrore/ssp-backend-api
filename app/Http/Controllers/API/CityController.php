<?php

namespace App\Http\Controllers\API;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Resources\CityResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class CityController extends BaseController
{

     /**
     * @OA\Get(
     *     path="/api/cities",
     *     tags={"City"},
     *     summary="Get authenticated cities",
     *     description="Returns the authenticated cities ",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Business Name"),
     *             @OA\Property(property="email", type="string", example="business@example.com")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    // Get all Citys
    public function index()
    {

        return City::all();
    }

    /**
     * @OA\Get(
     *     path="/api/cities-get-all-paginated",
     *     tags={"Categories"},
     *     summary="Get paginated categories",
     *     description="Returns a paginated list of categories.",
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
     *                     @OA\Property(property="title", type="string", example="Category Title"),
     *                     @OA\Property(property="description", type="string", example="Category Description"),
     *                     @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg")
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

    // Get paginated Citys with sorting
    public function getAllPaginated(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort', 'title');
        $currentPage = $request->input('current_page', 1);

        $items = City::orderBy($sortField)
            ->paginate($perPage, ['*'], 'page', $currentPage)
            ->appends(['sort' => $sortField, 'current_page' => $currentPage]);

        $data = [
            'data' => CityResource::collection($items->items()),
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
     *     path="/api/cities",
     *     tags={"City"},
     *     summary="Create a new city",
     *     description="Creates a new city",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string", description="City title"),
     *             @OA\Property(property="description", type="string", description="City description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="City created successfully"
     *     ),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */

    // Create a new City
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $city = City::create($validatedData);

        return response()->json(['success' => true, 'data' => $city], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/cities/{id}",
     *     tags={"City"},
     *     summary="Get a single city",
     *     description="Returns a single city by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="City ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City retrieved successfully"),
     *     @OA\Response(response=404, description="City not found")
     * )
     */


    // Get a single City by id
    public function show($id)
    {
        $city = City::findOrFail($id);
        return response()->json(['success' => true, 'data' => $city], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/cities/{id}",
     *     tags={"City"},
     *     summary="Update a city",
     *     description="Updates a city by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="City ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="City title"),
     *             @OA\Property(property="description", type="string", description="City description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City updated successfully"
     *     ),
     *     @OA\Response(response=404, description="City not found"),
     *     @OA\Response(response=400, description="Invalid input")
     * )
     */

    // Update a City
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $city = City::findOrFail($id);
        $city->update($validatedData);

        return response()->json(['success' => true, 'data' => $city], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/cities/{id}",
     *     tags={"City"},
     *     summary="Delete a city",
     *     description="Deletes a city by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="City ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string", example="City deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="City not found")
     * )
     */
    
    // Delete a City
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully'
        ], 200);
    }
}
