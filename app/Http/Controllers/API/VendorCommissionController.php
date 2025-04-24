<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorCommission;
use Illuminate\Support\Facades\Validator;
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
                'status' => 1, // 1 for unpaid 2 for paid 3 refunded and 4 for pending
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

    public function getUnpaid_VendorCommission_list(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        try {
            $commissions = VendorCommission::where('vendor_id', $request->vendor_id)
                ->where('status', 1)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Vendor commission records fetched successfully.',
                'data' => $commissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vendor commissions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function fetchVendorCommissions(Request $request, $vendorID)
    {
        $status = $request->has('status') ? (array) $request->status : [1, 2, 3];
        $perPage = $request->get('per_page', 10);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        try {
            $vendor = Vendor::find($vendorID);

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found.',
                ], 404);
            }

            $query = VendorCommission::where('vendor_id', $vendor->id)
                ->whereIn('status', $status);

            // Apply date filters
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            $commissions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Vendor commissions fetched successfully.',
                'data' => $commissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vendor commissions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update_status(array $ids, int $status): array
    {
        try {
            $updatedCount = VendorCommission::whereIn('id', $ids)
                    ->update(['status' => $status]);
    
            return [
                'success' => true,
                'message' => "$updatedCount commission(s) updated successfully.",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update commission statuses.',
                'error' => $e->getMessage(),
            ];
        }
    }
    

    public function getVendorCommission_WithPagination(Request $request)
    {
        $userID = $request->user_id; // or Auth::id()
        $vendor = Vendor::where('user_id', $userID)->first();
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found.',
            ], 404);
        }
        return $this->fetchVendorCommissions($request, $vendor->id);
    }

    public function getVendorCommission_Admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        return $this->fetchVendorCommissions($request, $validated['vendor_id']);
    }
}
