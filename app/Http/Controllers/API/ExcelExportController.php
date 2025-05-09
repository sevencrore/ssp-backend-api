<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\SkosListingExport;
use App\Exports\AdminProductTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Exports\AdminVendorCommissionExport;

class ExcelExportController extends Controller
{
    public function exportSkosListing(Request $request)
    {
        return Excel::download(new SkosListingExport($request), 'skos_listing_export.xlsx');
    }

    public function exportAdminProductTransactions(Request $request)
    {
        // Validate required fields
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // If validation passes, proceed with export
        return Excel::download(new AdminProductTransactionsExport($request), 'admin_product_transactions.xlsx');
    }

    public function exportAdminVendorCommissions(Request $request)
    {
        // Validate required fields
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        return Excel::download(new AdminVendorCommissionExport($request), 'admin_vendor_commissions.xlsx');
    }
}
