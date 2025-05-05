<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VendorController;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ConfigSetting;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AdminController extends BaseController
{
    //
    public function updatePassword(Request $request): JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',  // Ensure email is provided and is valid
            'password' => 'required|min:6',  // Minimum password length of 6
            'c_password' => 'required|same:password',  // Ensure the passwords match
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Password update validation failed',
            ], 422);
        }
        $admin = User::find($request->user_id);
        if ($admin->user_type != 99) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }

        // Retrieve the user by email
        $user = User::where('email', $request->email)->first();

        // If user not found, return error0
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Update the user's password
        $user->password = bcrypt($request->password);  // Hash the new password
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ], 200);
    }

    // to make the active or deactive the user
    public function setStatus(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean' // Ensure it is 0 or 1
        ]);

        $admin = User::find($request->user_id);
        if ($admin->user_type != 99) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_active = $request->is_active; // Set the passed value (0 or 1)
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'User activated successfully' : 'User deactivated successfully',
            'user' => ['id' => $user->id, 'name' => $user->name],
            'status' => $user->is_active
        ]);
    }

    //admin reports
    public function getTopReferrers(Request $request)
    {
        $perPage = $request->get('per_page', 20); // Default to 20

        // Step 1: Count how many users each user has referred
        $topReferrers = UserDetails::whereNotNull('referred_by')
            ->groupBy('referred_by')
            ->selectRaw('referred_by, COUNT(*) as referral_count')
            ->orderByDesc('referral_count');


        // Step 2: Paginate the top referrers
        $paginated = $topReferrers->paginate($perPage);

        // Step 3: Get user details of referrers in one query
        $referrerIds = $paginated->pluck('referred_by')->toArray();
        $referrerDetails = UserDetails::whereIn('user_id', $referrerIds)
            ->get()
            ->keyBy('user_id');

        // Step 4: Combine user info with referral count
        $transformed = $paginated->getCollection()->transform(function ($item) use ($referrerDetails) {
            $user = $referrerDetails->get($item->referred_by);

            return [
                'user_id' => $item->referred_by,
                'first_name' => $user->first_name ?? null,
                'last_name' => $user->last_name ?? null,
                'phone_1' => $user->phone_1 ?? null,
                'email' => $user->email ?? null,
                'referral_count' => $item->referral_count,
            ];
        });

        // Step 5: Return the response
        return response()->json([
            'success' => true,
            'data' => $transformed,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'first_page_url' => $paginated->url(1),
                'last_page_url' => $paginated->url($paginated->lastPage()),
                'next_page_url' => $paginated->nextPageUrl(),
                'prev_page_url' => $paginated->previousPageUrl(),
            ],
        ]);
    }

    function getChargesDeduction(float $amount): array
    {
        try {
            $config = ConfigSetting::first();

            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'ConfigSetting not found',
                ];
            }

            $adminPercentage = $config->admin_comission_percentage ?? 0;
            $tdsPercentage = $config->tds_charges_percentage ?? 0;

            // Calculate admin charges
            $adminChargesAmount = round(($adminPercentage / 100) * $amount, 2);
            $afterAdminDeduction = round($amount - $adminChargesAmount, 2);

            // Calculate TDS on the remaining amount
            $tdsChargesAmount = round(($tdsPercentage / 100) * $afterAdminDeduction, 2);
            $finalAmount = round($afterAdminDeduction - $tdsChargesAmount, 2);

            return [
                'success' => true,
                'final_deducted_amount' => $finalAmount,
                'admin_charges_amount' => $adminChargesAmount,
                'tds_charges_amount' => $tdsChargesAmount,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }


    public function getDashboard_count()
    {
        try {
            // get newly joined users count 
            $userDetailsController = new UserDetailsController();
            $new_users = $userDetailsController->today_new_users_count();
            if (!$new_users['success']) {
                throw new \Exception("failed to fetch the new users" . $new_users['error']);
            }
            // get total users count 
            $total_user_count = $userDetailsController->total_users_count();
            if (!$total_user_count['success']) {
                throw new \Exception("failed to fetch the total users count" . $total_user_count['error']);
            }
            // get total vendotrs count 
            $vendorcontroller = new VendorController();
            $total_vendor_count = $vendorcontroller->total_vendors_count();
            if (!$total_vendor_count['success']) {
                throw new \Exception("failed to fetch total vendor count" . $total_vendor_count['error']);
            }

            // get today orders count 
            $ordercontroller = new OrderController();
            $today_order_count = $ordercontroller->today_orders_count();
            if (!$today_order_count['success']) {
                throw new \Exception("Failed to fetch Today's Orders" . $today_order_count['success']);
            }

            // get today total amount
            $userpaymentcontroller = new UserPaymentController();
            $today_user_payment = $userpaymentcontroller->TodayUserPayments();
            if (!$today_user_payment['success']) {
                throw new \Exception("Failed to fetch UserPayment" . $today_user_payment['error']);
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'new_users_count' => $new_users['count'],
                    'total_users_count'   => $total_user_count['count'],
                    'total_vendors_count' => $total_vendor_count['count'],
                    'today_orders_count'  => $today_order_count['count'],
                    'today_user_payment'  => $today_user_payment['total_amount'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard counts.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateUserAddressByAdmin(Request $request): JsonResponse
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'email'          => 'required|email',     
            'first_name'     => 'nullable|string|max:255',
            'last_name'      => 'nullable|string|max:255',
            'district_name'  => 'required|string|max:255',
            'city_name'      => 'required|string|max:255',
            'city_id'        => 'nullable|integer',
            'address'        => 'required|string',
            'pin_code'       => 'required|string|max:10',
            'phone_number'   => 'required|string|max:15',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Check if requester is admin
        $admin = User::find($request->user_id);
        if (!$admin || $admin->user_type != 99) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access: Not an admin',
            ], 403);
        }

        // Find the target user by email
        $targetUser = User::where('email', $request->email)->first();
        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'Target user not found',
            ], 404);
        }

        // Find the address linked to target user
        $address = Address::where('user_id', $targetUser->id)->first();
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found for this user',
            ], 404);
        }

        // Update address
        $address->update($request->only([
            'first_name',
            'last_name',
            'district_name',
            'city_name',
            'city_id',
            'address',
            'pin_code',
            'phone_number',
            'latitude',
            'longitude'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'User address updated successfully',
            'data' => $address
        ], 200);
    }
}
