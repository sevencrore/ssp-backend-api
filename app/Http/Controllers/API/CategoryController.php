<?php

namespace App\Http\Controllers\API;

use App\Models\Category; // Change this to use the Category model
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    public function index()
    {
        $data = Category::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getAllPaginated(Request $request): JsonResponse
    {
        
        // $items = Product::paginate($request->per_page)->appends(['sort' => $request->title]);

        // Get the per_page value from the request or set a default value
        $perPage = $request->input('per_page', 10); // Default to 10 if not provided

        // Get the sort field from the request, defaulting to 'title'
        $sortField = $request->input('sort', 'title');

        // Get the current_page from the request, defaulting to 1
        $currentPage = $request->input('current_page', 1);

        // Paginate products with sorting
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

        return response()->json([
            'success' => true,
            'data' => $data
        ], 201);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $category = Category::create($request->all());
        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'image_url' => 'sometimes|nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['success' => true, 'data' => null], 204);
    }
}
