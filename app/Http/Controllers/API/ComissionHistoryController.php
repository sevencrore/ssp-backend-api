<?php

namespace App\Http\Controllers\API;

use App\Exports\PayoutReportExport;
use App\Http\Controllers\Controller;
use App\Models\ComissionHistory;
use App\Models\ConfigSetting;
use App\Models\User;
use App\Models\UserBank;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class ComissionHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $comissions = ComissionHistory::all();
        return response()->json($comissions);
    }



    public function addCommissionRecord($user_id, $comission_type, $referal_id, $amount, $description)
    {
        $user = User::find($user_id);
        if ($user && !$user->is_active) {

            return 'succes the uesr is inactive';
        }
        try {

            Log::info("$user_id and the $amount in the add comissionrecord");

            if ($user_id == $referal_id) {
                $comission_type = 2;
            }
            // Create a new ComissionHistory record
            $comissionHistory = ComissionHistory::create([
                'user_id' => $user_id,
                'comission_type' => $comission_type,
                'referal_id' => $referal_id,
                'amount' => $amount,
                'description' => $description,
            ]);

            Log::info("$user_id and the $amount in the add comissionrecord but not savaing");

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Commission record added successfully!',
                'data' => $comissionHistory,
            ], 201);
        } catch (\Exception $e) {
            // Handle exception and return error response
            Log::info("$e");
            return response()->json([
                'success' => false,
                'message' => 'Failed to add commission record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve commission history based on user ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommissionHistory(Request $request)
    {
        Log::info("the user_id in the getcomissionhistory controller");

        $user_id = $request->user_id;
        $perPage = $request->input('per_page', 10); // Default 10 if not provided

        try {
            // Fetch commission history with pagination
            $commissionHistory = ComissionHistory::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Check if any records found
            if ($commissionHistory->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No commission history found for this user.',
                ], 404);
            }

            // Return success response with pagination info
            return response()->json([
                'success' => true,
                'message' => 'Commission history retrieved successfully.',
                'data' => $commissionHistory->items(),  // the actual records
                'total' => $commissionHistory->total(), // total number of records
                'per_page' => $commissionHistory->perPage(), // number of records per page
                'current_page' => $commissionHistory->currentPage(), // current page number
                'last_page' => $commissionHistory->lastPage(), // last page number
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error fetching commission history for user ID $user_id: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve commission history.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comission_type' => 'required|integer',
            'referal_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $comission = ComissionHistory::create($validated);
        return response()->json($comission, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  ComissionHistory  $comissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ComissionHistory $comissionHistory)
    {
        return response()->json($comissionHistory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ComissionHistory  $comissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ComissionHistory $comissionHistory)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comission_type' => 'nullable|integer',
            'referal_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $comissionHistory->update($validated);
        return response()->json($comissionHistory);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ComissionHistory  $comissionHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ComissionHistory $comissionHistory)
    {
        $comissionHistory->delete();
        return response()->json(null, 204);
    }

    public function name(Request $request)
    {
        Log::info(("hello prabhu"));
        return response("hello prabhu");
    }

    /**
     * Get commission histories by user_id.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByUserId($userId)
    {
        $comissions = ComissionHistory::where('user_id', $userId)->get();
        return response()->json($comissions);
    }

    public function getAllPaginated(Request $request): JsonResponse
    {
        try {
            // Get query parameters
            $perPage = $request->query('per_page', 10);
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $isPaid = $request->query('is_paid');
            $userId = $request->query('userId');

            // Query builder
            $query = ComissionHistory::query();

            // Apply date filter logic
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            } elseif ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Apply is_paid filter if provided
            if ($request->has('is_paid') && !is_null($isPaid)) {
                $query->where('is_paid', $isPaid);
            }

            // Apply user filter if provided
            if ($request->has('user_id') && !empty($userId)) {
                $query->where('user_id', $userId);
            }

            // Get paginated data
            $commissions = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Check if no records found
            if ($commissions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No commission history found.',
                ], 404);
            }

            // Return successful response with pagination info
            return response()->json([
                'success' => true,
                'message' => 'Commission history retrieved successfully.',
                'data' => $commissions,
                'pagination' => [
                    'current_page' => $commissions->currentPage(),
                    'total_pages' => $commissions->lastPage(),
                    'per_page' => $commissions->perPage(),
                    'total_records' => $commissions->total(),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve commission history.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function generatecomissionReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $response = $this->getUserWisePayouts($startDate, $endDate);
        $data = $response->getData();

        if (!$data->success) {
            return $response;
        }

        $comission_historyIds = collect($data->data)->pluck('commission_history_ids')->flatten()->toArray();

        // $excelResponse = $this->exportPayoutsToExcel($data->data);

        // if ($excelResponse->getStatusCode() === 200) {
        //     $this->markPayoutsAsPaid($allPayoutIds);
        // }

        // return $excelResponse;
        // return [
        //     'payouts_id' => $allPayoutIds,
        //     'data' => $data
        // ];
        return Excel::download(new PayoutReportExport($data->data), 'payout_report.xlsx');
    }

    private function getUserWisePayouts($startDate, $endDate)
    {
        try {
            $payouts = ComissionHistory::whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ])
                ->where('is_paid', 0)
                ->get();


            if ($payouts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payout records found for the given date range.'
                ], 404);
            }

            // Fetch config settings for dynamic percentage calculation
            $config = ConfigSetting::first();
            $adminPercentage = ($config->admin_charges_percentage ?? 10) / 100;
            $tdsPercentage = ($config->tds_charges_percentage ?? 2) / 100;

            $grouped = $payouts->groupBy('user_id')->map(function ($rows, $userId) use ($adminPercentage, $tdsPercentage) {
                $totalAmount = $rows->sum('amount');
                $adminCharge = $totalAmount * $adminPercentage;
                $tds = $totalAmount * $tdsPercentage;
                $payable = $totalAmount - $adminCharge - $tds;
                $comission_historyIds = $rows->pluck('id')->toArray();

                // Fetch first bank record for this user
                $bank = UserBank::where('user_id', $userId)->first();
                // Skip if no bank details
                if (!$bank) {
                    return null;
                }

                return [
                    'user_id' => $userId,
                    'total' => $totalAmount,
                    'admin_charge' => $adminCharge,
                    'admin_percentage' => $adminPercentage * 100,  // Store as percentage (e.g., 10)
                    'tds' => $tds,
                    'tds_percentage' => $tdsPercentage * 100,      // Store as percentage (e.g., 2)
                    'payable' => $payable,
                    'commission_history_ids' => $comission_historyIds,
                    'bank_details' => $bank
                ];
            })->filter();
            return response()->json([
                'success' => true,
                'data' => $grouped
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing payouts.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
