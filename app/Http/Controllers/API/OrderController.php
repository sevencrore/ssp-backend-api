<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="Order",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="address_id", type="integer"),
 *     @OA\Property(property="order_status", type="integer"),
 *     @OA\Property(property="tracking_number", type="string"),
 *     @OA\Property(property="total_amount", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class OrderController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Retrieve all Orders",
     *     @OA\Response(
     *         response=200,
     *         description="Retrieve all Orders successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Retrieve all Orders successfully',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create a new Order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_status", "total_amount"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="address_id", type="integer"),
     *             @OA\Property(property="order_status", type="integer"),
     *             @OA\Property(property="tracking_number", type="string"),
     *             @OA\Property(property="total_amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer',
            'address_id' => 'nullable|integer',
            'order_status' => 'required|integer',
            'tracking_number' => 'nullable|string',
            'total_amount' => 'required|integer',
        ]);

        $order = Order::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order created successfully',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Retrieve a specific Order",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specified Order displayed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Specified Order displayed successfully',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Update a specific Order",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_status", "total_amount"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="address_id", type="integer"),
     *             @OA\Property(property="order_status", type="integer"),
     *             @OA\Property(property="tracking_number", type="string"),
     *             @OA\Property(property="total_amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'user_id' => 'nullable|integer',
            'address_id' => 'nullable|integer',
            'order_status' => 'required|integer',
            'tracking_number' => 'nullable|string',
            'total_amount' => 'required|integer',
        ]);

        $order->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order updated successfully',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Delete a specific Order",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ], 200);
    }
}
