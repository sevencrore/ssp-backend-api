<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Validator;

class BuyProductController extends Controller
{
    public function BuyProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'amount' => 'required|integer',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'subtotal' => 'required|numeric',
            'savings' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'cart_data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['user_id'] = $request->user_id;

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderId = $validatedData['razorpay_order_id'];
        $paymentId = $validatedData['razorpay_payment_id'];
        $signature = $validatedData['razorpay_signature'];
        // // Verify the payment signature
        // $attributes  = array('razorpay_signature'  => $signature,  'razorpay_payment_id'  => $paymentId, 'razorpay_order_id' => $orderId);
        // $signatureVerified  = $api->utility->verifyPaymentSignature($attributes);

        $generatedSignature = hash_hmac('sha256', $orderId . "|" . $paymentId, env('RAZORPAY_SECRET'));
        // Handle validation failure
        if (!$generatedSignature === $signature) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Invalid signature.',
            ], 400);
        }

        // Save payment first, so that it's available for refund if needed
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $paymentArray = [
            'user_id' => $user->id,
            'transaction_id' => $validatedData['transaction_id'],
            'amount' => $validatedData['amount'],
            'email' => $user->email,
            'razorpay_order_id' => $validatedData['razorpay_order_id'],
            'razorpay_payment_id' => $validatedData['razorpay_payment_id'],
            'razorpay_signature' => $validatedData['razorpay_signature'],
            'status' => 1, // 1=> received 2=> refunded 
        ];

        $paymentController = new UserPaymentController();
        $savePayment = $paymentController->saveUserPayment($paymentArray);

        if (!$savePayment) {
            return response()->json(['success' => false, 'message' => 'Payment saving failed'], 500);
        }

        DB::beginTransaction();
        try {
            $userDeatilController = new UserDetailsController();
            $is_eligible = $userDeatilController->CheckUser_minimum_order($user->id, $validatedData['grand_total']);
            if (!$is_eligible['success']) {
                throw new \Exception($is_eligible['message']);
            }

            $orderController = new OrderController();
            $order = $orderController->createOrder($validatedData);
            if (!$order['success']) {
                throw new \Exception("failed to create the Order" . $order['error']);
            }

            $orderitemController = new OrderItemController();
            $orderitem = $orderitemController->storeOrderItems($order['order_id'], $validatedData['cart_data'],$validatedData['user_id']);
            if (!$orderitem['success']) {
                throw new \Exception("failed to store the Orderitems" . $order['error']);
            }
            $cartIds = $orderitem['cartIds'];
            // now delete the cart data 
            Cart::whereIn('id', $cartIds)->delete();
            // Commit transaction if all operations are successful
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order Created successfully.', 'user_id' => $user->id, 'user_name' => $user->user_name, 'order' => $order['order']], 200);
        } catch (\Exception $e) {
            // Rollback transaction in case of failure
            DB::rollBack();
            // need to call the payment refund functionality
            try {
                $razorpayController = new RazorpayPaymentController();
                $refund = $razorpayController->refundfullPayment($validatedData['razorpay_payment_id']);

                if (!$refund['success']) {
                    throw new \Exception("Refund failed: " . $refund['message']);
                }
            } catch (\Exception $refundException) {
                return response()->json([
                    'success' => false,
                    'message' => "Transaction failed and refund failed: " . $refundException->getMessage(),
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
