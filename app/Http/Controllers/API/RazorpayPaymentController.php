<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

class RazorpayPaymentController extends BaseController
{
    public function createOrder(Request $request)
    {
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

            $CartController = new CartController();
            $FinalCartPrice = $CartController->calculateFinalCartPrice($request->user_id);
            if(!$FinalCartPrice['success']){
                return response()->json([
                    'success' => false, 
                    'message' => $FinalCartPrice['message'], 
                    'error' => $FinalCartPrice['error'],
                ], 500);
            }
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            // Create Razorpay Order
            $order = $api->order->create([
                'receipt' => 'order_' . time(),
                'amount' => $FinalCartPrice['grand_total'] * 100, // Convert to paise
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => [
                    'user_id' => $user->id,
                    'name' => $user->user_name,
                    'email' => $user->email,
                    'message' => 'user buying the products from the cart',
                ]
            ]);

            // Extract 'notes' properly
            $notes =  $order['notes']->toArray();
            // Prepare order data
            $orderData = [
                'user_id' => $user->id,
                'order_id' => $order['id'],
                'amount' => $FinalCartPrice['grand_total'],
                'currency' => 'INR',
                'status' => 'created', // Initial status
                'notes' => $notes,
            ];

            // Call the PaymentOrderController internally
            $paymentOrderController = new PaymentOrderController();
            $storeResponse = $paymentOrderController->storeOrder($orderData);

            if (!$storeResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to store payment order.',
                    'error' => $storeResponse['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'razorpay_key' => env('RAZORPAY_KEY'),
                'amount' => $FinalCartPrice['grand_total'],
                'user' => [
                    'name' => $user->user_name,
                    'email' => $user->email,
                ],
                'currency' => $order->currency,
                'store_name' => "Shri Siddhaprabhu Enterprises Pvt Ltd",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }
}
