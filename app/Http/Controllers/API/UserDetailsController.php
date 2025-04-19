<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comission;
use App\Models\ConfigSetting;
use App\Models\UserDetails;
use Illuminate\Http\JsonResponse;


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

    public function CheckUser_minimum_order($UserId, $grand_total)
    {
       // first check whether the user has already orderd any order in this month 
       $ordercontroller = new OrderController();
       $hasOrders = $ordercontroller->checkUserHasOrderThisMonth($UserId);
       if($hasOrders['success']){
            // if he already has the orders purchaseds in this mothn the apply the minimum amount to basepay amount
            $configsetting = ConfigSetting::first();
            $minimum_base_pay = $configsetting->minimum_basepay_amount;
            if ($grand_total < $minimum_base_pay) {
                return [
                    'success' => false,
                    'message' => "The minimum amount to place the order is $minimum_base_pay",
                ];
            }
            else{
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

        // Step 1: Fetch all commissions and index by id
        $comissions = Comission::all()->keyBy('id');

        // Step 2: Fetch direct referrals with commission_id
        $directReferrals = UserDetails::where('referred_by', $userId)
            ->select('user_id', 'first_name', 'last_name', 'phone_1','email', 'comission_id')
            ->get();

        // Step 3: Map each referral with their commission's minimum_order
        $result = $directReferrals->map(function ($referral) use ($comissions) {
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

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
