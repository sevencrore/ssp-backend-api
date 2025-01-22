<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseController
{
    // Get all products
    public function index()
    {
        $data = Product::all();
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

    
    public function CustomProductGetAllPaginated(Request $request): JsonResponse
{
    $perPage = $request->input('per_page', 10); 
    $sortField = $request->input('sort', 'title');
    $currentPage = $request->input('current_page', 1);

    $query = Product::join('product_variants', 'product_variants.product_id', '=', 'products.id')
        ->select(
            'products.id', 
            'products.title', 
            'products.description', 
            'products.image_url', 
            // 'products.price', 
            'products.priority', 
            'products.category_id', 
            'product_variants.title as product_variants_title'
        );

    // Optionally add sorting
    $query->orderBy($sortField);

    $items = $query->paginate($perPage, ['*'], 'page', $currentPage)
        ->appends(['sort' => $sortField, 'current_page' => $currentPage]);

    $data = [
        'data' => $items->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'product_variants_title' => $item->product_variants_title, // Moved here
                'description' => $item->description,
                'image_url' => $item->image_url,
                // 'price' => $item->price,
                'priority' => $item->priority,
                'category_id' => $item->category_id,
            ];
        }),
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
    ], 200);
}




    


    // Create a new product
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate as a file and an image
            'priority' => 'nullable|integer',
            'category_id' => 'required|integer|exists:category,id',
        ]);
    
        try {
            // Check if an image file is provided and store it
            if ($request->hasFile('image_url')) {
                $path = $request->file('image_url')->store('images', 'public');
                $validatedData['image_url'] = $path; // Save the file path as a string
            }
    
            // Create the product
            $product = Product::create($validatedData);
    
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'image_url' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Updated to handle actual image uploads
            
        ]);
    
        // Find the product or throw a 404 error if not found
        $product = Product::findOrFail($id);
        Log::info("$product");
        // Handle image upload if a file is provided
        if ($request->hasFile('image_url')) {
            // Delete the old image if it exists
            Log::info("inside the hasimage");
            if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
            }
    
            // Store the new image
            $path = $request->file('image_url')->store('images', 'public');
            $validatedData['image_url'] = $path; // Update the image URL field
        }
    
        // Update the product with the validated data
        $product->update($validatedData);
    
        // Return a success response with the updated product data
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ], 200); // Use HTTP 200 for successful updates
    }
    
    
    // delete multiple product
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
            
        }
    
        $count = Product::whereIn('id', $ids)->delete(); // Soft deletes the records

        return $this->sendResponse(null, "{$count} Products deleted successfully.");
    }

    // delete restore multiple product
    public function restoreMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Product::withTrashed()->whereIn('id', $ids)->restore(); // Restores the soft-deleted records

        return $this->sendResponse(null, "{$count} Products restored successfully.");
    }

    //  force delete multiple product
    public function forceDeleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Product::withTrashed()->whereIn('id', $ids)->forceDelete(); // Permanently deletes the records

        return $this->sendResponse(null, "{$count} Product permanently deleted.");
    }

    // trashed delete multiple product
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
