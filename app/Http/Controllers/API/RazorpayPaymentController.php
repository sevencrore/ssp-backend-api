<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\UserPayment;

class RazorpayPaymentController extends BaseController
{
    public function createOrder(Request $request)
    {
        try {
            $api = new Api(config('razorpay.key'), config('razorpay.secret'));

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
                'razorpay_key' => config('razorpay.key'),
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

    public function refundfullPayment($paymentID)
    {
        try {
            // Fetch the payment record from the database
            $payment = UserPayment::where('razorpay_payment_id', $paymentID)->first();

            if (!$payment) {
                throw new \Exception("Payment record not found");
            }

            // Initialize Razorpay API
            $api = new Api(config('razorpay.key'), config('razorpay.secret'));

            // Fetch payment from Razorpay
            $razorpayPayment = $api->payment->fetch($paymentID);

            if (!$razorpayPayment) {
                throw new \Exception("Invalid Razorpay Payment ID");
            }

            // Process refund
            $refund = $razorpayPayment->refund([
                'amount' => $razorpayPayment->amount, // Razorpay stores amounts in paise
            ]);

            // Update the payment record in the database
            $payment->status = 2;
            $updated = $payment->save();

            return [
                'success' => true,
                'message' => 'Refund successful',
                'refund_id' => $refund['id']
            ];
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            return ['success' => false, 'message' => 'Bad request: ' . $e->getMessage()];
        } catch (\Razorpay\Api\Errors\ServerError $e) {
            return ['success' => false, 'message' => 'Razorpay server error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()];
        }
    }
}
