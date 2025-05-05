<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comission;
use App\Models\ConfigSetting;
use App\Models\UserDetails;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserDetailsController extends Controller
{

    // Get all user details
    public function index(): JsonResponse
    {
        $userDetails = UserDetails::all(); // Fetch all user details

        return response()->json(['success' => true, 'data' => $userDetails]);
    }


    // Store a new user detail
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_1' => 'required|string|max:15',
            'phone_2' => 'nullable|string|max:15',
            'email' => 'required|string|email|max:255|unique:user_details,email',
            'user_id' => 'required|integer',
            'aadhar_number' => 'required|integer',
            'referral_code' => 'required|string',
        ]);

        $userDetail = UserDetails::create($validatedData); // Create a new user detail

        return response()->json(['success' => true, 'data' => $userDetail], 201);
    }

    public function show($id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $userDetail]);
    }

    // Update a specific user detail
    public function update(Request $request, $id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        $validatedData = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone_1' => 'sometimes|required|string|max:15',
            'phone_2' => 'sometimes|nullable|string|max:15',
            'email' => 'sometimes|required|string|email|max:255|unique:user_details,email,' . $userDetail->id,
            'user_id' => 'required|integer',
            'aadhar_number' => 'required|integer',
            'referral_code' => 'required|string',
        ]);

        $userDetail->update($validatedData); // Update the user detail

        return response()->json(['success' => true, 'data' => $userDetail]);
    }

    public function getUserDetailsByEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            $userDetails = UserDetails::where('email', $request->email)->first();
    
            if (!$userDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'User details not found for the provided email',
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'User details retrieved successfully',
                'data' => $userDetails,
            ], 200);
    
        } catch (\Exception $e) {
            Log::error("Error retrieving user details by email: " . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'An internal error occurred while retrieving user details',
            ], 500);
        }
    }
    



    // Delete a specific user detail
    public function destroy($id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        $userDetail->delete(); // Delete the user detail

        return response()->json(['success' => true, 'message' => 'User detail deleted successfully']);
    }

    public function getUser_minimum_order(Request $request)
    {
        try {
            $UserId = $request->user_id;

            // First check whether the user has already ordered any order in this month 
            $ordercontroller = new OrderController();
            $hasOrders = $ordercontroller->checkUserHasOrderThisMonth($UserId);

            if ($hasOrders['success']) {
                // If user already has orders in this month, apply the minimum amount from config
                $configsetting = ConfigSetting::first();
                $minimum_base_pay = $configsetting->minimum_basepay_amount;

                return response()->json([
                    'success' => true,
                    'message' => "The minimum base pay order amount is ₹$minimum_base_pay.",
                    'minimum_order' => $minimum_base_pay,
                ]);
            }

            $userDetail = UserDetails::where('user_id', $UserId)->first();
            $comission = Comission::find($userDetail->comission_id);

            return response()->json([
                'success' => true,
                'message' => "The minimum order amount is ₹$comission->minimum_order.",
                'minimum_order' => $comission->minimum_order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function CheckUser_minimum_order($UserId, $grand_total)
    {
        // first check whether the user has already orderd any order in this month 
        $ordercontroller = new OrderController();
        $hasOrders = $ordercontroller->checkUserHasOrderThisMonth($UserId);
        if ($hasOrders['success']) {
            // if he already has the orders purchaseds in this mothn the apply the minimum amount to basepay amount
            $configsetting = ConfigSetting::first();
            $minimum_base_pay = $configsetting->minimum_basepay_amount;
            if ($grand_total < $minimum_base_pay) {
                return [
                    'success' => false,
                    'message' => "The minimum amount to place the order is $minimum_base_pay",
                ];
            } else {
                return [
                    'success' => true,
                    'message' => 'Grand Total is above the minimum_base_pay order amount.'
                ];
            }
        }

        $userDetail = UserDetails::where('user_id', $UserId)->first();
        $comission_id = $userDetail->comission_id;

        $comission = Comission::where('id', $comission_id)->first();

        if ($grand_total < $comission->minimum_order) {
            return [
                'success' => false,
                'message' => "The minimum amount to place the order is $comission->minimum_order",
            ];
        }
        return [
            'success' => true,
            'message' => 'Grand Total is above the minimum order amount.'
        ];
    }

    public function getDirectReferralsDetails(Request $request)
    {
        $userId = $request->user_id;
        $perPage = $request->get('per_page', 10); // Default to 10

        // Step 1: Fetch all commissions and index by id
        $comissions = Comission::all()->keyBy('id');

        // Step 2: Paginate direct referrals
        $directReferrals = UserDetails::where('referred_by', $userId)
            ->select('user_id', 'first_name', 'last_name', 'phone_1', 'email', 'comission_id')
            ->paginate($perPage);

        // Step 3: Transform paginated data
        $transformedData = $directReferrals->getCollection()->transform(function ($referral) use ($comissions) {
            $comission = $comissions->get($referral->comission_id);

            return [
                'user_id' => $referral->user_id,
                'first_name' => $referral->first_name,
                'last_name' => $referral->last_name,
                'phone_1' => $referral->phone_1,
                'email' => $referral->email,
                'comission_id' => $referral->comission_id,
                'minimum_order' => $comission ? $comission->minimum_order : null,
            ];
        });

        // Step 4: Send paginated response
        return response()->json([
            'success' => true,
            'data' => $transformedData,
            'pagination' => [
                'current_page' => $directReferrals->currentPage(),
                'last_page' => $directReferrals->lastPage(),
                'per_page' => $directReferrals->perPage(),
                'total' => $directReferrals->total(),
                'from' => $directReferrals->firstItem(),
                'to' => $directReferrals->lastItem(),
                'first_page_url' => $directReferrals->url(1),
                'last_page_url' => $directReferrals->url($directReferrals->lastPage()),
                'next_page_url' => $directReferrals->nextPageUrl(),
                'prev_page_url' => $directReferrals->previousPageUrl(),
            ],
        ]);
    }


    public function today_new_users_count()
    {
        try {
            $count = UserDetails::whereDate('created_at', Carbon::today())->count();

            return [
                'success' => true,
                'count' => $count,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch today\'s users.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function total_users_count()
    {
        try {
            $count = UserDetails::count();

            return [
                'success' => true,
                'count' => $count,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch total users count.',
                'error' => $e->getMessage(),
            ];
        }
    }
    public function GetUserDetails(Request $request)
    {
        try {
            Log::info("User ID: $request->user_id");

            // Get the user details for the given user
            $userDetails = UserDetails::where('user_id', $request->user_id)->first();

            // Check if user details exist
            if ($userDetails) {
                Log::info($userDetails);
                return response()->json([
                    'success' => true,
                    'message' => 'User details found',
                    'data' => $userDetails
                ], 200); // Status 200 for success
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No user details found for this user'
                ], 404); // Status 404 if not found
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving user details: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the user details'
            ], 500); // Status 500 on exception
        }
    }
}
