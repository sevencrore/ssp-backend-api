<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class VendorTransactionsController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'required|exists:vendors,id',
                'vendor_commission_id_array' => 'required|array',
                'total_amount' => 'required|numeric',
                'admin_commission_amount' => 'required|numeric',
                'tds_charges_amount' => 'required|numeric',
                'amount_paid' => 'required|numeric',
                'transaction_id' => 'required|string|unique:vendor_transactions,transaction_id',
                'status' => 'required|integer', // 1 for unpaid, 2 for paid, 3 refunded, 4 pending
                'attachment' => 'nullable|file|mimes:jpeg,jpg,png,gif,pdf|max:20480',
            ]);

            $validated['paid_by_user_id'] = $request->user_id;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('attachments', $filename, 'public');
                $validated['attachment'] = $path;
            }


            $transaction = VendorTransaction::create($validated);

            // update the vendor comission status 
            $vendorcommissioncontroller = new VendorCommissionController();
            $updated_status = $vendorcommissioncontroller->update_status($validated['vendor_commission_id_array'], $validated['status']);

            if (!$updated_status['success']) {
                throw new \Exception("Status updation failed for  vendor comission" . $updated_status['error']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vendor transaction created successfully.',
                'data' => $transaction,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create vendor transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function fetchVendorTransactions(Request $request, $vendorID)
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

            $query = VendorTransaction::where('vendor_id', $vendor->id)
                ->whereIn('status', $status);

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Vendor transactions fetched successfully.',
                'data' => $transactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vendor transactions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getVendorTransactions_WithPagination(Request $request)
    {
        $userID = $request->user_id; // or Auth::id()
        $vendor = Vendor::where('user_id', $userID)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found.',
            ], 404);
        }

        return $this->fetchVendorTransactions($request, $vendor->id);
    }

    public function getVendorTransactions_Admin(Request $request)
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

        return $this->fetchVendorTransactions($request, $validated['vendor_id']);
    }
}
