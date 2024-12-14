<?php

namespace App\Http\Controllers\API;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="order_id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="quantity", type="integer"),
 *     @OA\Property(property="price", type="integer"),
 *     @OA\Property(property="total_amount", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class OrderItemController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/order-items",
     *     tags={"Order Items"},
     *     summary="Retrieve all Order Items",
     *     @OA\Response(
     *         response=200,
     *         description="Retrieve all Order Items successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OrderItem")),
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = OrderItem::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * @OA\Post(
     *     path="/api/order-items",
     *     tags={"Order Items"},
     *     summary="Create a new Order Item",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "product_id", "user_id", "quantity", "price", "total_amount"},
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="price", type="integer"),
     *             @OA\Property(property="total_amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order Item created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderItem"),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'product_id' => 'required|integer',
            'user_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|integer',
            'total_amount' => 'required|integer',
        ]);

        $orderItem = OrderItem::create($request->all());
        return response()->json(['success' => true, 'data' => $orderItem], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/order-items/{orderItem}",
     *     tags={"Order Items"},
     *     summary="Retrieve a specific Order Item",
     *     @OA\Parameter(
     *         name="orderItem",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specified Order Item displayed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderItem"),
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        return response()->json(['success' => true, 'data' => $orderItem]);
    }


    public function getOrderItemsByOrderId($orderId)
    {
        // Retrieve all order items for the given order_id
        $orderItems = OrderItem::where('order_id', $orderId)->get();

        if ($orderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found for this order.',
            ], 404);
        }

        // Prepare the response with order item details and product data
        $response = $orderItems->map(function ($item) {
            $product = Product::find($item->product_id);

            return [
                'product_id' => $item->product_id,
                'image_url' => $product ? $product->image_url : null,
                'product_title' => $product ? $product->title : null,
                'quantity' => $item->quantity,
                'unit_quantity' => $item->unit_quantity,
                'unit_title' => $item->unit_title,
                'price' => $item->price,
                'discount' => $item->discount,
                'total_amount' => $item->total_amount,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $response,
            'message' => 'Order items retrieved successfully.',
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/order-items/{orderItem}",
     *     tags={"Order Items"},
     *     summary="Update a specific Order Item",
     *     @OA\Parameter(
     *         name="orderItem",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="price", type="integer"),
     *             @OA\Property(property="total_amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order Item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderItem"),
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'sometimes|required|integer',
            'product_id' => 'sometimes|required|integer',
            'user_id' => 'sometimes|required|integer',
            'quantity' => 'sometimes|required|integer',
            'price' => 'sometimes|required|integer',
            'total_amount' => 'required|integer',
        ]);

        $orderItem = OrderItem::findOrFail($id);
        $orderItem->update($request->all());
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    /**
     * @OA\Delete(
     *     path="/api/order-items/{orderItem}",
     *     tags={"Order Items"},
     *     summary="Delete a specific Order Item",
     *     @OA\Parameter(
     *         name="orderItem",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order Item deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Order item deleted successfully',
            'data' => null
        ], 200);
    }
}
