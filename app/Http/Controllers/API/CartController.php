<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class CartController extends BaseController
{
    public function index(): JsonResponse
    {
        $data = Cart::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function show(int $id): JsonResponse
    {
        $cart = Cart::findOrFail($id);
        return response()->json(['success' => true, 'data' => $cart]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        $cartItem = Cart::create($validatedData);
        return response()->json(['success' => true, 'data' => $cartItem], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|required|integer',
            'product_id' => 'sometimes|required|integer',
        ]);

        $cart = Cart::findOrFail($id);
        $cart->update($validatedData);
        return response()->json(['success' => true, 'data' => $cart]);
    }

    public function destroy(int $id): JsonResponse
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart deleted successfully',
            'data' => null
        ], 200);
    }
}
