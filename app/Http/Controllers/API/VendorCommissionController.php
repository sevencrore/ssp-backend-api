<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\VendorCommission;
use Illuminate\Http\Request;

class VendorCommissionController extends Controller
{
    public function InsertVendor_Commission_Record($vendor_id, $order_id)
    {
        //get the order record
        try {
            // Retrieve the order record
            $order = Order::findOrFail($order_id);
            $amount = $order->grand_total;

            // Prepare the data for insertion
            $vendorCommissionData = [
                'vendor_id' => $vendor_id,
                'order_id' => $order_id,
                'amount' => $amount,
                'status' => 1, // Default status: 1 (pending/unpaid)
            ];

            // Create the vendor commission record
            $commission = VendorCommission::create($vendorCommissionData);

            return [
                'success' => true,
                'data' => $commission,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to insert vendor commission record.',
                'error' => $e->getMessage(),
            ];
        }

    }
}