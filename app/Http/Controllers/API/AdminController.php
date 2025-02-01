<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
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
        if( $admin->user_type != 99){
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
        if( $admin->user_type != 99){
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
            'user' => ['id'=>$user->id,'name'=>$user->name],
            'status' => $user->is_active
        ]);
    }

}
