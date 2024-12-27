<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Earning;
use App\Models\ConfigSetting;
use App\Models\Comission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

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
            'phone_1' => 'required',               // Required field
            'phone2' => 'nullable',               // Optional field
            'aadhar_number' => 'nullable',        // Optional field
            'comission_id' => 'required',
        ]);
    
        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422);
        }
    
        // Use only validated data
        $validatedData = $validator->validated();
    
        // Create user data using validated fields
        $userData = [
            'name' => $validatedData['user_name'],
            'email' => $validatedData['email'],
            'user_name' => $validatedData['user_name'],
            'password' => bcrypt($validatedData['password']), // Hashing the password
            'last_name' => $validatedData['last_name'],
        ];
    
        $user = User::create($userData);
    
        // Prepare details for UserDetails table
        $details = [
            'first_name' => $validatedData['user_name'], // Assuming user_name is first name
            'last_name' => $validatedData['last_name'],
            'phone_1' => $validatedData['phone_1'],
            'email' => $validatedData['email'],
            'user_id' => $user->id,
            // Optional fields
            'aadhar_number' => $validatedData['aadhar_number'] ?? null,
            'referral_code' => $user->id,
            'comission_id' => $validatedData['comission_id'] ,
        ];
    
        $userDetails = UserDetails::create($details);
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

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['id'] = $user->id; // Directly use the user's ID
        $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully.',
        ], 200);
    }

    public function registerWthReferral(Request $request): JsonResponse
    {
        Log::info('I am here');
    
        // Validator definition
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_name' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'referral_code' => 'required',
            'last_name' => 'required',
            'phone_1' => 'required',               // Required field
            'phone2' => 'nullable',               // Optional field
            'aadhar_number' => 'nullable',        // Optional field
            'comission_id' => 'required',
        ]);
    
        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422);
        }
    
        // Use only validated data
        $validatedData = $validator->validated();
    
        // Create user data using validated fields
        $userData = [
            'name' => $validatedData['user_name'],
            'email' => $validatedData['email'],
            'user_name' => $validatedData['user_name'],
            'password' => bcrypt($validatedData['password']), // Hashing the password
            'referral_code' => $validatedData['referral_code'],
            'last_name' => $validatedData['last_name'],
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

        // get user_details by referral code . here user_id is referral_id
        $referrer = UserDetails::where('referral_code', $validatedData['referral_code'])->first();
        $referrer_id = null;

    
        // Prepare details for UserDetails table
        $details = [
            'first_name' => $validatedData['user_name'], // Assuming user_name is first name
            'last_name' => $validatedData['last_name'],
            'phone_1' => $validatedData['phone_1'],
            'email' => $validatedData['email'],
            'user_id' => $user->id,
            // Optional fields
            'aadhar_number' => $validatedData['aadhar_number'] ?? null,
            'referral_code' => $user->id,
            'comission_id' => $validatedData['comission_id'] ,
            'referred_by' => $referrer->user_id ,
        ];
    
        $userDetails = UserDetails::create($details);

        // get user->id => reg_user_id
        $reg_user_id = $user->id;

        
        if ($referrer) {
            $earningController = new EarningController();

            $referrer_id = $referrer->user_id;
            
            for ($i = 1; $i <= $max_depth; $i++) {
                if (!$referrer_id) {
                    break;  // Exit the loop if no valid user ID is returned
                }
                $referrer_id = $earningController->updateEstimatedsales($referrer_id, $commission->minimum_order, $i);
            }
        } else {
            // if referral code is not found then create new earning row
        }

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['id'] = $user->id; // Directly use the user's ID
        $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully.',
        ], 200);
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
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['id'] = $user->id; // Directly use the user's ID
            $success['name'] = $user->name;

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
}
