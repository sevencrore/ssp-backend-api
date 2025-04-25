<?php

namespace App\Http\Controllers\API;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Log;

class OrderItemController extends BaseController
{
    public function index()
    {
        $data = OrderItem::all();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function storeOrderItems($orderId, $cartdata, $UserId)
    {
        try {
            $orderItems = [];
            foreach ($cartdata as $cartItem) {
                $orderItems[] = [
                    'order_id' => $orderId,
                    'user_id' => $UserId,
                    'product_id' => $cartItem['product_id'],
                    'product_variant_id' => $cartItem['product_variant_id'],
                    'quantity' => $cartItem['quantity'],
                    'unit_quantity' => $cartItem['unit_quantity'],
                    'unit_title' => $cartItem['unit_title'],
                    'price' => $cartItem['price'],
                    'discount' => $cartItem['discount'],
                    'total_amount'  => $cartItem['quantity'] * $cartItem['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // Collect the cart item id for deletion later
                $cartIds[] = $cartItem['id'];
            }
            // Insert data into order_items table
            $orderitems = OrderItem::insert($orderItems);
            return [
                'success' => true,
                'message' => 'OrderItems stored successfully.',
                'orderitems' => $orderitems,
                'cartIds' => $cartIds,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to store the OrderItems.',
                'error' => $e->getMessage()
            ];
        }
    }

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


    public function show($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    public function fetchOrderItemsWithProductData($orderId)
    {
        // Retrieve all order items for the given order_id
        $orderItems = OrderItem::where('order_id', $orderId)->get();

        if ($orderItems->isEmpty()) {
            return null;
        }

        // Prepare the data with order item details and product data
        return $orderItems->map(function ($item) {
            $product = Product::find($item->product_id);
            $productvarient = ProductVariant::find($item->product_variant_id);

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
                'product_variant_title' =>  $productvarient->title,
            ];
        });
    }


    public function getOrderItemsByOrderId($orderId)
    {
        $response = $this->fetchOrderItemsWithProductData($orderId);

        if (is_null($response)) {
            return response()->json([
                'success' => false,
                'message' => 'No items found for this order.',
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $response,
            'message' => 'Order items retrieved successfully.',
        ], 200);
    }

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
