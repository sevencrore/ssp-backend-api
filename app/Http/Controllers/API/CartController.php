<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CartController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/cart",
     *     tags={"Cart"},
     *     summary="Get all cart items",
     *     description="Fetches all items in the cart.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=2),
     *                     @OA\Property(property="product_variants_id", type="integer", example=5),
     *                     @OA\Property(property="discount", type="integer", example=10),
     *                     @OA\Property(property="created_at", type="string", example="2024-10-15T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $data = Cart::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * @OA\Get(
     *     path="/api/cart/{id}",
     *     tags={"Cart"},
     *     summary="Get a specific cart item",
     *     description="Returns a single cart item by ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the cart item",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cart item not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $cart = Cart::findOrFail($id);
        return response()->json(['success' => true, 'data' => $cart]);
    }


    // controller for the getting the cart details based on the user id
    public function getCartByUserId(int $userId): JsonResponse
    {
        try {
            $cartItems = DB::table('carts')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->join('product_variants', 'carts.product_variants_id', '=', 'product_variants.id')
                ->join('unit', 'product_variants.unit_id', '=', 'unit.id')
                ->select(
                    'carts.quantity',
                    'products.title as product_title',
                    'products.category_id',
                    'products.image_url',
                    'product_variants.title as variant_title',
                    'product_variants.description',
                    'product_variants.price',
                    'product_variants.discount',
                    'product_variants.unit_id',
                    'product_variants.unit_quantity',
                    'unit.title as unit_title'
                )
                ->where('carts.user_id', $userId)
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


    /**
     * @OA\Post(
     *     path="/api/cart",
     *     tags={"Cart"},
     *     summary="Create a new cart item",
     *     description="Stores a new cart item with user_id, product_id, product_variants_id, and discount.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "product_id", "product_variants_id", "discount"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=2),
     *             @OA\Property(property="product_variants_id", type="integer", example=5),
     *             @OA\Property(property="discount", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cart item created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/cart/{id}",
     *     tags={"Cart"},
     *     summary="Update an existing cart item",
     *     description="Updates a cart item with new data, including product_variants_id and discount.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the cart item to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=2),
     *             @OA\Property(property="product_variants_id", type="integer", example=5),
     *             @OA\Property(property="discount", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cart item not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/cart/{id}",
     *     tags={"Cart"},
     *     summary="Delete a cart item",
     *     description="Removes a cart item by ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the cart item to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cart deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Cart item not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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
