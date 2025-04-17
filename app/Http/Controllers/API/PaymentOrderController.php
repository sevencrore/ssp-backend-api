<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentOrder;

class PaymentOrderController extends Controller
{
    public function storeOrder($orderData)
    {
        try {
            
            $paymentOrder = PaymentOrder::create([
                'user_id' => $orderData['user_id'],
                'order_id' => $orderData['order_id'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'],
                'status' => $orderData['status'] ?? 'created', // Default to 'created'
                'notes' => $orderData['notes'] ?? [],
            ]);

            return [
                'success' => true,
                'message' => 'Payment order stored successfully.',
                'payment_order' => $paymentOrder
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to store payment order.',
                'error' => $e->getMessage()
            ];
        }
    }
}
