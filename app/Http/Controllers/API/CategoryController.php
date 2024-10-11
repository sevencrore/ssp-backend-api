<?php

namespace App\Http\Controllers\API;

use App\Models\Category; // Change this to use the Category model
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    public function index()
    {
        $data = Category::all();
        return response()->json(['success' => true, 'data' => $data]);
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
