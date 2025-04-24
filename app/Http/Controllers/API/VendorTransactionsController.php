<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VendorTransaction;
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
                'amount_paid' => 'required|integer',
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
            $updated_status = $vendorcommissioncontroller->update_status($validated['vendor_commission_id_array'],$validated['status']);

            if (!$updated_status['success']) {
                throw new \Exception("Status updation failed for  vendor comission" .$updated_status['error'] );
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


    
}
