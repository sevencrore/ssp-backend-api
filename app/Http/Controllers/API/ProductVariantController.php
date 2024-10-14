<?php

namespace App\Http\Controllers\API;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Resources\ProductVariantResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class ProductVariantController extends BaseController
{
    // Get all product variants
    public function index(): JsonResponse
    {
        $productVariants = ProductVariant::all(); // Fetch all product variants
        return $this->sendResponse(ProductVariantResource::collection($productVariants), 'Product variants retrieved successfully.');
    }

    // Get all product variants with pagination
    public function getAllPaginated(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10); // Default to 10 items per page
        $sortField = $request->input('sort', 'title');
        $currentPage = $request->input('current_page', 1);

        $items = ProductVariant::orderBy($sortField)
            ->paginate($perPage, ['*'], 'page', $currentPage)
            ->appends(['sort' => $sortField, 'current_page' => $currentPage]);

        $data = [
            'data' => ProductVariantResource::collection($items->items()),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'next_page_url' => $items->nextPageUrl(),
                'prev_page_url' => $items->previousPageUrl(),
            ],
        ];

        return $this->sendResponse($data, 'Paginated product variants retrieved successfully.');
    }

    // Store a new product variant
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'product_id' => 'required|string', // Ensures product_id exists in products table
            'category_id' => 'required|string', // Ensure category_id is also validated
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'required|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $productVariant = ProductVariant::create($validatedData);

        return $this->sendResponse(new ProductVariantResource($productVariant), 'Product variant created successfully.', 201);
    }

    // Get a specific product variant by ID
    public function show($id): JsonResponse
    {
        $productVariant = ProductVariant::findOrFail($id);
        return $this->sendResponse(new ProductVariantResource($productVariant), 'Product variant retrieved successfully.', 200);
    }

    // Update a product variant
    public function update(Request $request, $id): JsonResponse
    {
        $productVariant = ProductVariant::findOrFail($id);

        $validatedData = $request->validate([
            'product_id' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|string', // Ensure category_id is also validated
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image_url' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $productVariant->update($validatedData);

        return $this->sendResponse(new ProductVariantResource($productVariant), 'Product variant updated successfully.', 200);
    }

    // Delete a specific product variant
    public function destroy($id): JsonResponse
    {
        $productVariant = ProductVariant::findOrFail($id);
        $productVariant->delete();

        return $this->sendResponse(null, 'Product variant deleted successfully.');
    }

    // Delete multiple product variants
    public function deleteMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = ProductVariant::whereIn('id', $ids)->delete(); // Soft deletes the records

        return $this->sendResponse(null, "{$count} Product variants deleted successfully.");
    }

    // Restore multiple product variants
    public function restoreMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = ProductVariant::withTrashed()->whereIn('id', $ids)->restore(); // Restores the soft-deleted records

        return $this->sendResponse(null, "{$count} Product variants restored successfully.");
    }

    // Force delete multiple product variants
    public function forceDeleteMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = ProductVariant::withTrashed()->whereIn('id', $ids)->forceDelete(); // Permanently deletes the records

        return $this->sendResponse(null, "{$count} Product variants permanently deleted.");
    }

    // Get trashed product variants
    public function trashedMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $trashedProductVariants = ProductVariant::onlyTrashed()->whereIn('id', $ids)->get(); // Retrieves only specified soft-deleted records

        return $this->sendResponse(ProductVariantResource::collection($trashedProductVariants), 'Trashed product variants retrieved successfully.');
    }
}