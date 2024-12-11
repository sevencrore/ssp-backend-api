<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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


    /**
     * Store order and order items.
     */
    public function storeOrder(Request $request)
    {
        // Validate request payload
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'subtotal' => 'required|numeric',
            'savings' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'cart_data' => 'required|array',
        ]);

        // Generate a unique tracking number
        $trackingNumber = 'TRK' . strtoupper(uniqid());

        // Use DB transaction for atomicity
        DB::beginTransaction();

        try {
            // Store data into orders table
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_status' => 0, // Default status
                'total_amount' => $validated['subtotal'],
                'discount' => $validated['savings'],
                'grand_total' => $validated['grand_total'],
                'tracking_number' => $trackingNumber,
            ]);

            // Retrieve the created order ID
            $orderId = $order->id;

            // Prepare order items data
            $orderItems = [];
            foreach ($validated['cart_data'] as $cartItem) {
                $orderItems[] = [
                    'order_id' => $orderId,
                    'user_id' => $validated['user_id'],
                    'product_id' => $cartItem['product_id'],
                    'product_variant_id' => $cartItem['product_variant_id'],
                    'quantity' => $cartItem['quantity'],
                    'unit_quantity' => $cartItem['unit_quantity'],
                    'unit_title' => $cartItem['unit_title'],
                    'price' => $cartItem['price'],
                    'discount' =>$cartItem['discount'],
                    'total_amount'  => $cartItem['quantity'] * $cartItem['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert data into order_items table
            OrderItem::insert($orderItems);

            // Commit the transaction
            DB::commit();

            // Return response
            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $orderId,
                'tracking_number' => $trackingNumber,
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Return error response
            return response()->json([
                'message' => 'Failed to place order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


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
