<?php
namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    // Get all Categorys
    public function index()
    {
        return Category::all();
    }

    // Get paginated Categorys with sorting
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
                'prev_page_url' => $items->previousPageUrl(),
            ]
        ];

        return response()->json(['success' => true, 'data' => $data], 201);
    }

    // Create a new Category (without price)
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'required|string',
        ]);

        $Category = Category::create($validatedData);

        return response()->json(['success' => true, 'data' => $Category], 201);
    }

    // Get a single Category by id
    public function show($id)
    {
        $Category = Category::findOrFail($id);
        return response()->json(['success' => true, 'data' => $Category], 201);
    }

    // Update a Category
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image_url' => 'sometimes|required|string',
        ]);

        $Category = Category::findOrFail($id);
        $Category->update($validatedData);

        // return response()->json($Category, 200);
        return response()->json(['success' => true, 'data' => $Category], 201);
    }

    // Delete a Category
    public function destroy($id)
    {
        $Category = Category::findOrFail($id);
        $Category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
