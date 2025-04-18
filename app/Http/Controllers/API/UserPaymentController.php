<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPayment;
use Illuminate\Support\Facades\Log;
use Exception;

class UserPaymentController extends Controller
{
    public function saveUserPayment(array $paymentArray){
        // Store the payment data in the database
         $savepayment = UserPayment::create($paymentArray);
         if(!$savepayment){
            return false;
         }
         return $savepayment;
    }

    public function getAdminproduct_transactions(Request $request)
    {
        try {
            // // Ensure the filter is always an array
            // $filter = $request->has('is_paid') ? (array) $request->is_paid : [0, 1]; // Default filter

            // Date filter setup
            $dateFilter = [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date
            ];

            // Set pagination limit (default 10 if not provided)
            $perPage = $request->has('per_page') ? (int) $request->per_page : 10;

            // Fetch all payouts without filtering by user_id
            $query = UserPayment::orderBy('created_at', 'desc');

            // Apply date filters
            if (!is_null($dateFilter['start_date']) && !is_null($dateFilter['end_date'])) {
                $query->whereBetween('created_at', [$dateFilter['start_date'], $dateFilter['end_date']]);
            } elseif (!is_null($dateFilter['start_date'])) {
                $query->where('created_at', '>=', $dateFilter['start_date']);
            } elseif (!is_null($dateFilter['end_date'])) {
                $query->where('created_at', '<=', $dateFilter['end_date']);
            }

            // Retrieve payouts with pagination
            $transactions = $query->paginate($perPage)->appends([    
                'per_page' => $perPage
            ]);

            // Check if transactions exist
            if ($transactions->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No Transactions found.',
                    'data'    => [],
                ];
            }

            return [
                'success' => true,
                'message' => 'All transactions retrieved successfully',
                'data'    => $transactions,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page'     => $transactions->perPage(),
                    'total'        => $transactions->total(),
                    'last_page'    => $transactions->lastPage(),
                    'next_page_url' => $transactions->nextPageUrl(),
                    'prev_page_url' => $transactions->previousPageUrl(),
                ],
            ];
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Error retrieving all transactions: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred while retrieving transactions.',
                'error'   => $e->getMessage(),
                'data'    => [],
            ];
        }
    }

}
