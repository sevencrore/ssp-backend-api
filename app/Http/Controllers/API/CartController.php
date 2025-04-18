<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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


    // controller for the getting the cart details based on the user id
    public function getCartByUserId(Request $request): JsonResponse
    {
        try {
            $cartItems = DB::table('carts')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->join('product_variants', 'carts.product_variants_id', '=', 'product_variants.id')
                ->join('unit', 'product_variants.unit_id', '=', 'unit.id')
                ->select(
                    'carts.id as id',
                    'carts.quantity',
                    'products.id as product_id',
                    'products.title as product_title',
                    'products.category_id',
                    'products.image_url',
                    'product_variants.id as product_variant_id',
                    'product_variants.title as variant_title',
                    'product_variants.description',
                    'product_variants.price',
                    'product_variants.discount',
                    'product_variants.unit_id',
                    'product_variants.unit_quantity',
                    'unit.title as unit_title'
                )
                ->where('carts.user_id', $request->user_id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items found in the cart for this user.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cartItems,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the cart items.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'product_variants_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cartItem = Cart::create($validatedData);
        return response()->json(['success' => true, 'data' => $cartItem], 201);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|required|integer',
            'product_id' => 'sometimes|required|integer',
            'product_variants_id' => 'sometimes|required|integer',
            'discount' => 'sometimes|required|integer|min:0',
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

    function updateCartItems(Request $request): JsonResponse
    {
        $cartData = $request->cartdata;
        try {
            foreach ($cartData as $item) {
                if (!isset($item['id'])) {
                    continue; // or handle as needed
                }

                Cart::where('id', $item['id'])->update([
                    'product_id' => $item['product_id'] ?? null,
                    'product_variants_id' => $item['product_variant_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart items updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating cart items.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    function calculateFinalCartPrice($userId)
    {
        try {
            $cartItems = Cart::where('user_id', $userId)->get();
            $total_price = 0;
            $total_discount = 0;

            foreach ($cartItems as $item) {
                $variant = ProductVariant::find($item->product_variants_id);

                if (!$variant) {
                    continue; // skip if the variant doesn't exist
                }

                $price = $variant->price;
                $discount = $variant->discount ?? 0;

                $itemTotal = $price * $item->quantity;
                $itemDiscountAmount = ($price * $discount / 100) * $item->quantity;

                $total_price += $itemTotal;
                $total_discount += $itemDiscountAmount;
            }

            $grand_total = $total_price - $total_discount;

            return [
                'success' => true,
                'total_price' => round($total_price, 2),
                'total_discount' => round($total_discount, 2),
                'grand_total' => round($grand_total, 2)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Something went wrong while calculating the cart price.',
                'error' => $e->getMessage()
            ];
        }
    }
}
