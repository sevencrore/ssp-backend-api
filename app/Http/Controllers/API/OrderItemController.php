<?php

namespace App\Http\Controllers\API;

use App\Models\OrderItem;
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
