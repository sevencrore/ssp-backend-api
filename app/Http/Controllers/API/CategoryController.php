<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/category",
     *     tags={"Categories"},
     *     summary="Get all categories",
     *     description="Returns a list of all categories.",
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
     *                     @OA\Property(property="title", type="string", example="Category Title"),
     *                     @OA\Property(property="description", type="string", example="Category Description"),
     *                     @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $data = Category::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/category-get-all-paginated",
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
    public function getAllPaginated(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort', 'title');
        $currentPage = $request->input('current_page', 1);

        $items = Category::orderBy($sortField)
            ->paginate($perPage, ['*'], 'page', $currentPage)
            ->appends(['sort' => $sortField, 'current_page' => $currentPage]);

        $data = [
            'data' => CategoryResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'next_page_url' => $items->nextPageUrl(),
                'prev_page_url' => $items->previousPageUrl()
            ]
        ];

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/category",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     description="Creates a new category.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Category"),
     *             @OA\Property(property="description", type="string", example="Category Description"),
     *             @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Category"),
     *                 @OA\Property(property="description", type="string", example="Category Description"),
     *                 @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input.")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $category = Category::create($validatedData);
        return response()->json(['success' => true, 'data' => $category], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/category/{id}",
     *     tags={"Categories"},
     *     summary="Get a specific category",
     *     description="Returns details of a specific category.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Category Title"),
     *                 @OA\Property(property="description", type="string", example="Category Description"),
     *                 @OA\Property(property="image_url", type="string", example="http://example.com/image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found.")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        return response()->json(['success' => true, 'data' => $category]);
    }

    /**
     * @OA\Put(
     *     path="/api/category/{id}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     description="Updates an existing category.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Category"),
     *             @OA\Property(property="description", type="string", example="Updated Description"),
     *             @OA\Property(property="image_url", type="string", example="http://example.com/updated_image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Category"),
     *                 @OA\Property(property="description", type="string", example="Updated Description"),
     *                 @OA\Property(property="image_url", type="string", example="http://example.com/updated_image.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found.")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'image_url' => 'sometimes|nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validatedData);
        return response()->json(['success' => true, 'data' => $category]);
    }

    /**
     * @OA\Delete(
     *     path="/api/category/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     description="Deletes a specific category.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=204,
     *         description="Category deleted successfully."
     *     ),
     *     @OA\Response(response=404, description="Category not found.")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['success' => true, 'data' => null], 204);
    }
}
