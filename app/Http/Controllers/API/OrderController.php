<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseController
{
    // Display a listing of the resource
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Retrieve all Orders successfully',
        ]);
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer',
            'address_id' => 'nullable|integer',
            'order_status' => 'required|integer',
            'tracking_number' => 'nullable|string',
        ]);

        $order = Order::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order created successfully',
        ], 201);
    }

    // Display the specified resource
    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'specified Order displayed successfully',
        ]);
    }

    // Update the specified resource in storage
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'user_id' => 'nullable|integer',
            'address_id' => 'nullable|integer',
            'order_status' => 'required|integer',
            'tracking_number' => 'nullable|string',
        ]);

        $order->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order updated successfully',
        ]);
    }

    // Remove the specified resource from storage
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ], 200);
    }
}
