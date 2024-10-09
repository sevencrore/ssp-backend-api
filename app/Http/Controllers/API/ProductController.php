<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    // Get all products
    public function index()
    {
        return Product::all();
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
        $items = Product::orderBy($sortField)
        ->paginate($perPage, ['*'], 'page', $currentPage)
        ->appends(['sort' => $sortField, 'current_page' => $currentPage]);


        $data = [
            'data' => ProductResource::collection($items->items()),
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


    // Create a new product
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($validatedData);

        return response()->json([
            'success' => true,
            'data' => $product
        ], 201);
    }

    // Get a single product by id
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ], 201);
    }

    // Update a product
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image_url' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'data' => $product
        ], 201);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
            
        }
    
        $count = Product::whereIn('id', $ids)->delete(); // Soft deletes the records

        return $this->sendResponse(null, "{$count} Products deleted successfully.");
    }

    public function restoreMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Product::withTrashed()->whereIn('id', $ids)->restore(); // Restores the soft-deleted records

        return $this->sendResponse(null, "{$count} Products restored successfully.");
    }

    public function forceDeleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Product::withTrashed()->whereIn('id', $ids)->forceDelete(); // Permanently deletes the records

        return $this->sendResponse(null, "{$count} Product permanently deleted.");
    }

    public function trashedMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $trashedProduct = Product::onlyTrashed()->whereIn('id', $ids)->get(); // Retrieves only specified soft-deleted records

        return $this->sendResponse($trashedProduct, 'Trashed Product retrieved successfully.');
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        // return response()->json(['success' => 'Product deleted successfully'], 200);
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);

    }
}
