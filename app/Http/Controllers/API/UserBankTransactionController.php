<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComissionHistory;
use App\Models\UserBankTransaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserBankTransactionController extends Controller
{
    /**
     * List transactions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $transactions = UserBankTransaction::with('user')
                ->latest()
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Transactions fetched successfully.',
                'data' => $transactions,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transactions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllPaginated(Request $request): JsonResponse
    {
        try {
            // 1️⃣ Query parameters
            $perPage = $request->query('per_page', 10);
            // $startDate = $request->query('start_date');
            // $endDate = $request->query('end_date');
            $status = $request->query('status');
            $userId = $request->query('userId');

            // 2️⃣ Base query
            $query = UserBankTransaction::query();

            // 3️⃣ Date filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            } elseif ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // 4️⃣ Status filter
            if ($request->has('status') && !is_null($status)) {
                $query->where('status', $status);
            }

            // 5️⃣ User filter
            if ($request->filled('userId')) {
                $query->where('user_id', $userId);
            }

            // 6️⃣ Pagination with relationship
            $transactions = $query
                ->with('user:id,user_name,phone_number')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // 7️⃣ Empty check
            if ($transactions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No bank transactions found.',
                ], 404);
            }

            // 8️⃣ Success response
            return response()->json([
                'success' => true,
                'message' => 'Bank transactions retrieved successfully.',
                'data' => $transactions,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'total_pages' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total_transactions' => $transactions->total(),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bank transactions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store transaction
     */
    public function store(Request $request)
    {
        // 1️⃣ Inline validation
        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer|exists:users,id',
            'commission_ids_array' => 'required|array|min:1',
            'commission_ids_array.*' => 'integer|exists:comission_history,id',

            'customer_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'account_number' => 'required|string|max:50',

            'amount' => 'required|numeric|min:0',
            'admin_charges' => 'required|numeric|min:0',
            'tsd_charges' => 'required|numeric|min:0',
            'amount_tobe_paid' => 'required|numeric|min:0',

            'status' => 'required|in:1,2,3',
            'utr_number' => 'nullable|string|max:100',
            'failure_reason' => 'nullable|string|max:255',
            'processed_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 'validation_failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 2️⃣ Store bank transaction
            $transaction = UserBankTransaction::create([
                'user_id' => $request->userId,
                'commission_ids_array' => $request->commission_ids_array, // auto-cast JSON
                'customer_name' => $request->customer_name,
                'bank_name' => $request->bank_name,
                'branch' => $request->branch,
                'ifsc_code' => $request->ifsc_code,
                'account_number' => $request->account_number,
                'amount' => $request->amount,
                'admin_charges' => $request->admin_charges,
                'tsd_charges' => $request->tsd_charges,
                'amount_tobe_paid' => $request->amount_tobe_paid,
                'status' => $request->status,
                'utr_number' => $request->utr_number,
                'failure_reason' => $request->failure_reason,
                'processed_at' => $request->processed_at,
            ]);

            // 3️⃣ Update commissions ONLY if status = SUCCESS
            if ((int) $request->status === 1) {
                ComissionHistory::whereIn('id', $request->commission_ids_array)
                    ->update([
                        'is_paid' => 1, // example: 1 = paid
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bank transaction recorded successfully.',
                'transaction_id' => $transaction->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Transaction failed.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show single transaction
     */
    public function show(int $id): JsonResponse
    {
        try {
            $transaction = UserBankTransaction::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Transaction fetched successfully.',
                'data' => $transaction,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update transaction
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $transaction = UserBankTransaction::findOrFail($id);

            $transaction->update($request->only([
                'commission_ids_array',
                'status',
                'utr_number',
                'failure_reason',
                'processed_at',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully.',
                'data' => $transaction,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete transaction
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            UserBankTransaction::findOrFail($id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
