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
    public function index(): JsonResponse
    {
        $productVariants = ProductVariant::all();
        return $this->sendResponse(ProductVariantResource::collection($productVariants), 'Product variants retrieved successfully.');
    }

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


 public function store(Request $request): JsonResponse
 {
    try {
            $validatedData = $request->validate([
                'product_id' => 'required|string', 
                //  'category_id' => 'required|string',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'image_url' => 'nullable|string',
                'price' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'unit_id' => 'required|string',
                'unit_quantity' => 'required|numeric',
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('images', 'public');
                $validatedData['image_url'] = $path;
            }
        
            $productVariant = ProductVariant::create($validatedData);
        
            return $this->sendResponse(new ProductVariantResource($productVariant), 'Product variant created successfully.', 201);
     }catch (\Exception $e) {
        // Handle the exception
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }

}


    
    public function show($id): JsonResponse
    {
        $productVariant = ProductVariant::join('products',  'product_variants.product_id', '=', 'products.id')
        ->where('product_variants.id', $id)
        ->get(['product_variants.*',  'products.title as product_title', ]);
        
        // $productVariant = ProductVariant::join('products',  'product_variants.product_id', '=', 'products.id')
        // ->join('category', 'product_variants.category_id', '=', 'category.id')
        // ->where('product_variants.product_id', $id)
        // ->get(['product_variants.*',  'products.title as product_title',  'category.title as category_title']);
        
        

        return response()->json([
            'success' => true,
            'data' => $productVariant
        ], 201);
    }

    
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
        'unit_id' => 'sometimes|required|integer', 
        'unit_quantity' => 'sometimes|required|numeric', 
    ]);

    $productVariant->update($validatedData);
    return response()->json([
        'success' => true,
        'data' => $productVariant
    ], 201);

}
public function getProductsWithVariants(Request $request): JsonResponse
{
    $perPage = $request->input('per_page', 10); 
    $currentPage = $request->input('current_page', 1);

    $products = Product::with('variants')->paginate($perPage, ['*'], 'page', $currentPage)
        ->appends(['per_page' => $perPage, 'current_page' => $currentPage]);

    $formattedProducts = $products->map(function ($product) {
        return [
            'product_id' => $product->id,
            'image_url' => $product->image_url,
            'product_variants' => $product->variants->map(function ($variant) {
                return [
                    'product_variant_id' => $variant->id,
                    'title' => $variant->title,
                    'description' => $variant->description,
                    'price' => $variant->price,
                    'discount' => $variant->discount,
                    'unit_id' => $variant->unit_id,
                    'unit_quantity' => $variant->unit_quantity,
                    'unit_title' => $variant->unit ? $variant->unit->title : null, 
                ];
            })->toArray(), 
        ];
    })->toArray(); 

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






    public function destroy($id): JsonResponse
    {
        $productVariant = ProductVariant::findOrFail($id);
        $productVariant->delete();

        return $this->sendResponse(null, 'Product variant deleted successfully.');
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = ProductVariant::whereIn('id', $ids)->delete(); // Soft deletes the records

        return $this->sendResponse(null, "{$count} Product variants deleted successfully.");
    }

    public function restoreMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = ProductVariant::withTrashed()->whereIn('id', $ids)->restore(); // Restores the soft-deleted records

        return $this->sendResponse(null, "{$count} Product variants restored successfully.");
    }

    public function forceDeleteMultiple(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = ProductVariant::withTrashed()->whereIn('id', $ids)->forceDelete(); // Permanently deletes the records

        return $this->sendResponse(null, "{$count} Product variants permanently deleted.");
    }

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
