<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Earning;
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
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, // Change to false
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422); // 422 Unprocessable Entity for validation errors
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully.',
        ], 200);
    }


    public function registerWthReferral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'referral_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, // Change to false
                'data' => $validator->errors(),
                'message' => 'Register Validation failed',
            ], 422); // 422 Unprocessable Entity for validation errors
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']); 

        $user = User::create($input);

        // get user->id => reg_user_id
        $reg_user_id = $user->id;

        // get user_details by referral code . here user_id is referral_id
        $referrer = UserDetails::where('referral_code', $input['referral_code'])->first();
        $referrer_id = null;
        if(referrer) {
            $referrer_id = $referrer->user_id;

             // Get earnings by user_id is nothing but referral_id
            $earningRow = Earning::where('user_id', $referrer_id)->first();

            if(!$earningRow) {
                $earningData = [
                    'referral_incentive' => 30,
                    'sale_value_estimated' => 3000,
                    'sale_actual_value' => 0,
                    'wallet_amount' => 0,
                    'self_purchase_total' => 0,
                    'first_referral_purchase_total' => 0,
                    'second_referral_purchase_total' => 0,
                    'user_id' => $referrer_id,
                ];
                Earning::create($earningData);
            } else {
                $earningRow->referral_incentive =  $earningRow->referral_incentive + 300;

                $earningRow->update(earningRow);     
            }
        }

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
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
            $success['name'] = $user->name;

            return response()->json([
                'success' => true,
                'data' => $success,
                'message' => 'User login successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false, // Change to false
                'data' => 'Unauthorised',
                'message' => 'User login Failed.',
            ], 401); // 401 Unauthorized for failed login
        }
    }
}
