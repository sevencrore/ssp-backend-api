<?php

namespace App\Http\Controllers\API;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductVariantResource;
use App\Http\Resources\ProductVariantCategoryProductResource;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
 // Store a new product variant
public function store(Request $request): JsonResponse
{

    $validatedData = $request->validate([
        'product_id' => 'required|string', 
        'category_id' => 'required|string',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'image_url' => 'string',
        'price' => 'required|numeric',
        'discount' => 'nullable|numeric',
        'unit_id' => 'required|integer',
        'unit_quantity' => 'required|numeric',
        'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $path = $request->file('pv_image')->store('images', 'public');
    $validatedData['image_url'] = $path;

    $productVariant = ProductVariant::create($validatedData);

    return $this->sendResponse(new ProductVariantResource($productVariant), 'Product variant created successfully.', 201);
}


    // Get a specific product variant by ID
    public function show($id): JsonResponse
    {
        
        $productVariant = ProductVariant::join('products',  'product_variants.product_id', '=', 'products.id')
        ->join('category', 'product_variants.category_id', '=', 'category.id')
        ->where('product_variants.product_id', $id)
        ->get(['product_variants.*',  'products.title as product_title',  'category.title as category_title']);
        
        // Properly log the array
        // Log::info('Product_Variant: ' . json_encode($productVariant));

        return response()->json([
            'success' => true,
            'data' => $productVariant
        ], 201);
    }

    // Update a product variant
  // Update a product variant
public function update(Request $request, $id): JsonResponse
{
    $productVariant = ProductVariant::findOrFail($id);

    $validatedData = $request->validate([
        'product_id' => 'sometimes|required|numeric',
        'category_id' => 'sometimes|required|numeric',
        'title' => 'sometimes|required|string|max:255',
        'description' => 'sometimes|required|string',
        'image_url' => 'sometimes|required|string',
        'price' => 'sometimes|required|numeric',
        'discount' => 'nullable|numeric',
        'unit_id' => 'sometimes|required|integer', // Add validation for unit_id
        'unit_quantity' => 'sometimes|required|numeric', // Add validation for unit_quantity
    ]);

    $productVariant->update($validatedData);
    return response()->json([
        'success' => true,
        'data' => $productVariant
    ], 201);

}
public function getProductsWithVariants(Request $request): JsonResponse
{
    $perPage = $request->input('per_page', 10); // Default to 10 items per page
    $currentPage = $request->input('current_page', 1);

    $products = Product::with('variants')->paginate($perPage, ['*'], 'page', $currentPage)
        ->appends(['per_page' => $perPage, 'current_page' => $currentPage]);

    $formattedProducts = $products->map(function ($product) {
        return [
            'product_id' => $product->id,
            'product_variants' => $product->variants->map(function ($variant) {
                return [
                    'product_variant_id' => $variant->id,
                    'title' => $variant->title,
                    'description' => $variant->description,
                    'image_url' => $variant->image_url,
                    'price' => $variant->price,
                    'discount' => $variant->discount,
                    'unit_id' => $variant->unit_id,
                    'unit_quantity' => $variant->unit_quantity,
                ];
            })->toArray(), // Convert collection to array
        ];
    })->toArray(); // Convert collection to array

    return response()->json([
        'success' => true,
        'data' => [
            'data' => $formattedProducts,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
        ],
    ]);
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
