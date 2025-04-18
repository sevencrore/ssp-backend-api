<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordEmailController extends Controller
{
    public function sendOtp(Request $request)
    {
        // Step 1: Validate request with custom messages
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'exists:users,email'
            ],
        ], [
            'email.required' => 'email is required.',
            'email.exists' => 'This email Id is not registered.',
        ]);

        // Step 2: Return validation errors if any
        if ($validator->fails()) {
            $firstError = $validator->errors()->first(); // Get the first error message
            return response()->json([
                'success' => false,
                'message' => $firstError,
            ], 422);
        }

        // Step 3: Generate OTP
        $otp = rand(100000, 999999);

        // Step 4: Store OTP
        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(10)]
        );

        // Step 5: Send OTP via Email provider (add your own logic here)
        $emailController = new EMailController();
        $subject = "forgot password reset Otp";
        $sendOTP = $emailController->sendOtp($request->email,$otp,$subject,);
        if(!$sendOTP['success']){
            return response()->json([
                'success' => false,
                'message' => $sendOTP['message'],
                'error' => $sendOTP['error'],
                // 'otp' => $otp, // Only include for testing or debugging (remove in production)
            ], 500);
        }

        // Step 6: Return success response
        return response()->json([
            'success' => true,
            'message' => 'OTP sent to email successfully.',
            // 'otp' => $otp, // Only include for testing or debugging (remove in production)
        ], 200);
    }
    
    public function verifyOtp(Request $request)
    {
        // Step 1: Validate with custom messages
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
        ], [
            'email.required' => 'Email Id is required.',
            'email.exists' => 'This Email Id is not registered.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be a 6-digit number.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'c_password.required' => 'Confirm Password is required.',
            'c_password.same' => 'Confirm Password must match Password.',
        ]);

        // Step 2: Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        $validatedData = $validator->validated();

        $record = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record || Carbon::now()->greaterThan($record->expires_at)) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::where('email', $validatedData['email'])->first();
        $user->password = bcrypt($validatedData['password']);
        $user->save();

        // Clean up
        $record->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }

}
