<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        return Product::all();
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
        return Product::findOrFail($id);
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

        return response()->json($product, 200);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
