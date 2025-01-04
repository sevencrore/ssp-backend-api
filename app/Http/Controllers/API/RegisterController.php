<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\CustomerVendorController;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Earning;
use App\Models\ConfigSetting;
use App\Models\Comission;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RegisterController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Authentication"},
     *     summary="Register a user",
     *     description="Register a new user and get access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "c_password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="c_password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful registration"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_name' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'referral_code' => 'nullable',
            'last_name' => 'required',
            'phone_1' => 'required',
            'phone2' => 'nullable',
            'aadhar_number' => 'nullable',
            'comission_id' => 'required',
            'user_type' => 'required',
            'pincode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422);
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();

        try {
            // Create user data using validated fields
            $userData = [
                'name' => $validatedData['user_name'],
                'email' => $validatedData['email'],
                'user_name' => $validatedData['user_name'],
                'password' => bcrypt($validatedData['password']),
                'last_name' => $validatedData['last_name'],
                'user_type'  => $validatedData['user_type'],
            ];

            $user = User::create($userData);

            if (!$user) {
                throw new \Exception('User registration failed');
            }

            // Prepare details for UserDetails table
            $details = [
                'first_name' => $validatedData['user_name'],
                'last_name' => $validatedData['last_name'],
                'phone_1' => $validatedData['phone_1'],
                'email' => $validatedData['email'],
                'user_id' => $user->id,
                'aadhar_number' => $validatedData['aadhar_number'] ?? null,
                'referral_code' => $user->id,
                'comission_id' => $validatedData['comission_id'],
                'pincode' => $validatedData['pincode'],
            ];

            $userDetails = UserDetails::create($details);

            // Auto assigning the vendor to customer
            $customervendorcontroller = new CustomerVendorController();
            $vendorAssignment = $customervendorcontroller->autoAsssignCustomerVendor($user->id, $validatedData['pincode']);
            Log::info("The auto-assigned customer vendor $vendorAssignment");

            $configSetting = ConfigSetting::find(1);

            // Find a record by its ID
            $commission = Comission::find($validatedData['comission_id']);
            $earningData = [
                'referral_incentive' => 0,
                'sale_value_estimated' => $commission->minimum_order,
                'sale_actual_value' => 0,
                'wallet_amount' => 0,
                'self_purchase_total' => 0,
                'first_referral_purchase_total' => 0,
                'second_referral_purchase_total' => 0,
                'user_id' => $user->id,
            ];
            Earning::create($earningData);

            DB::commit();

            $success['id'] = $user->id;
            $success['name'] = $user->name;

            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User registered successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration.',
            ], 500);
        }
    }

    public function registerWthReferral(Request $request): JsonResponse
    {
        Log::info('I am here');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_name' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'referral_code' => 'required',
            'last_name' => 'required',
            'phone_1' => 'required',
            'phone2' => 'nullable',
            'aadhar_number' => 'nullable',
            'comission_id' => 'required',
            'user_type' => 'required',
            'pincode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422);
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();

        try {
            $userData = [
                'name' => $validatedData['user_name'],
                'email' => $validatedData['email'],
                'user_name' => $validatedData['user_name'],
                'password' => bcrypt($validatedData['password']),
                'referral_code' => $validatedData['referral_code'],
                'last_name' => $validatedData['last_name'],
                'user_type'  => $validatedData['user_type'],
            ];

            $user = User::create($userData);

            $commission = Comission::find($validatedData['comission_id']);
            $configSetting = ConfigSetting::find(1);
            $max_depth = $configSetting->max_level;

            $earningData = [
                'referral_incentive' => 0,
                'sale_value_estimated' => $commission->minimum_order,
                'sale_actual_value' => 0,
                'wallet_amount' => 0,
                'self_purchase_total' => 0,
                'first_referral_purchase_total' => 0,
                'second_referral_purchase_total' => 0,
                'user_id' => $user->id,
            ];
            Earning::create($earningData);

            $referrer = UserDetails::where('referral_code', $validatedData['referral_code'])->first();
            $referrer_id = $referrer ? $referrer->user_id : null;

            $details = [
                'first_name' => $validatedData['user_name'],
                'last_name' => $validatedData['last_name'],
                'phone_1' => $validatedData['phone_1'],
                'email' => $validatedData['email'],
                'user_id' => $user->id,
                'aadhar_number' => $validatedData['aadhar_number'] ?? null,
                'referral_code' => $user->id,
                'comission_id' => $validatedData['comission_id'],
                'referred_by' => $referrer_id,
                'pincode' => $validatedData['pincode'],
            ];
            UserDetails::create($details);

            $customervendorcontroller = new CustomerVendorController();
            $vendorAssignment = $customervendorcontroller->autoAsssignCustomerVendor($user->id, $validatedData['pincode']);
            Log::info("The auto-assigned customer vendor $vendorAssignment");

            if ($referrer) {
                $earningController = new EarningController();
                for ($i = 1; $i <= $max_depth; $i++) {
                    if (!$referrer_id) {
                        break;
                    }
                    $referrer_id = $earningController->updateEstimatedsales($referrer_id, $commission->minimum_order, $i);
                }
            }

            DB::commit();

            $success['id'] = $user->id;
            $success['name'] = $user->name;

            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User registered successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration with referral failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration.',
            ], 500);
        }
    }


    //  vendor registration 
    public function registerVendor(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_name' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'last_name' => 'required',
            'phone_1' => 'required',
            'phone_2' => 'nullable',
            'aadhar_number' => 'nullable',
            'business_name' => 'required',
            'address' => 'nullable',
            'pincode' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'user_type' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422);
        }
    
        $validatedData = $validator->validated();
    
        DB::beginTransaction();
    
        try {
            // Create user data using validated fields
            $userData = [
                'name' => $validatedData['user_name'],
                'email' => $validatedData['email'],
                'user_name' => $validatedData['user_name'],
                'password' => bcrypt($validatedData['password']),
                'last_name' => $validatedData['last_name'],
                'user_type'  => $validatedData['user_type'],
            ];
    
            $user = User::create($userData);
    
            // Prepare details for Vendor table
            $vendordata = [
                'first_name' => $validatedData['user_name'],
                'last_name' => $validatedData['last_name'],
                'phone_1' => $validatedData['phone_1'],
                'phone_2' => $validatedData['phone_2'],
                'email' => $validatedData['email'],
                'business_name' => $validatedData['business_name'],
                'pincode' => $validatedData['pincode'],
                'address' => $validatedData['address'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'user_id' => $user->id,
                'aadhar_number' => $validatedData['aadhar_number'] ?? null,
            ];
    
            Vendor::create($vendordata);
    
            DB::commit();
    
            $success['id'] = $user->id;
            $success['name'] = $user->name;
    
            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'Vendor registered successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Vendor registration failed: " . $e->getMessage());
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration.',
            ], 500);
        }
    }
    

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login a user",
     *     description="Login and get access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function login(Request $request): JsonResponse
    {   Log::info("$request->user_id");
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken($user->id)->plainTextToken;
            $success['id'] = $user->id; // Directly use the user's ID
            $success['name'] = $user->name;
            $success['user_type'] = $user->user_type;

            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User login successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Unauthorised',
                'message' => 'User login Failed.',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        // Ensure the user is authenticated
        if ($request->user()) {
            Log::info("$request->user_id");
            // Revoke the token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'User not authenticated.'
        ], 401);
    }
}
