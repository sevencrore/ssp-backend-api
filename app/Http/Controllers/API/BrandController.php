<?php

namespace App\Http\Controllers\API;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class BrandController extends BaseController
{

     /**
     * @OA\Get(
     *     path="/api/brand",
     *     tags={"Brand"},
     *     summary="Get all brands",
     *     description="Returns a list of all brands.",
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
     *                     @OA\Property(property="title", type="string", example="Brand Title"),
     *                     @OA\Property(property="description", type="string", example="Brand Description")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    // Get all Brands
    public function index()
    {

        return Brand::all();
    }


    /**
     * @OA\Get(
     *     path="/api/brands-get-all-paginated",
     *     tags={"Brand"},
     *     summary="Get paginated brands",
     *     description="Returns a paginated list of brands.",
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
     *                     @OA\Property(property="title", type="string", example="Brand Title"),
     *                     @OA\Property(property="description", type="string", example="Brand Description")
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

    // Get paginated Brands with sorting
    public function getAllPaginated(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort', 'title');
        $currentPage = $request->input('current_page', 1);

        $items = Brand::orderBy($sortField)
            ->paginate($perPage, ['*'], 'page', $currentPage)
            ->appends(['sort' => $sortField, 'current_page' => $currentPage]);

        $data = [
            'data' => BrandResource::collection($items->items()),
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
     *     path="/api/brand",
     *     tags={"Brand"},
     *     summary="Create a new brand",
     *     description="Creates a new brand.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Brand"),
     *             @OA\Property(property="description", type="string", example="Brand Description")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Brand created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Brand"),
     *                 @OA\Property(property="description", type="string", example="Brand Description")
     *             )
     * )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input.")
     * )
     */

    // Create a new Brand
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $brand = Brand::create($validatedData);

        return response()->json(['success' => true, 'data' => $brand], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/brand/{id}",
     *     tags={"Brand"},
     *     summary="Get a specific brand",
     *     description="Returns details of a specific brand.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Brand Title"),
     *                 @OA\Property(property="description", type="string", example="Brand Description")     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Brand not found.")
     * )
     */

     
    // Get a single Brand by id
    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json(['success' => true, 'data' => $brand], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/brand/{id}",
     *     tags={"Brand"},
     *     summary="Update a brand",
     *     description="Updates an existing brand.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Brand"),
     *             @OA\Property(property="description", type="string", example="Updated Description")     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Brand updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Brand"),
     *                 @OA\Property(property="description", type="string", example="Updated Description")     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Brand not found.")
     * )
     */

    // Update a Brand
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update($validatedData);

        return response()->json(['success' => true, 'data' => $brand], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/brand/{id}",
     *     tags={"Brand"},
     *     summary="Delete a brand",
     *     description="Deletes a specific brand.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=204,
     *         description="Brand deleted successfully."
     *     ),
     *     @OA\Response(response=404, description="Brand not found.")
     * )
     */
    
    // Delete a Brand
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully'
        ], 200);
    }
}
