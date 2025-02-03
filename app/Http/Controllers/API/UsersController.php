<?php

namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class UsersController extends BaseController
{
    public function index()
    {
        // Logic to list all users
    }

    //get user based on the search 
    public function getUsersBySearch(Request $request)
    {   
        $admin = User::find($request->user_id);
        if( $admin->user_type != 99){
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 404);
        }
        Log::info("user _id is $request->user_id");
        // Base query with left join
        $query = UserDetails::leftJoin('address', 'user_details.user_id', '=', 'address.user_id')
            ->leftJoin('users', 'user_details.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'user_details.first_name',
                'user_details.middle_name',
                'user_details.last_name',
                'user_details.phone_1',
                'user_details.phone_2',
                'user_details.email',
                'user_details.user_id',
                'user_details.comission_id',
                'user_details.pincode',
                'user_details.referred_by',
                'address.address as user_address',
                'users.is_active as is_active',
            )
            ->orderBy('user_details.created_at', 'desc'); // Order by created_at in descending order

        // Apply search filter if 'search' parameter is present
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('user_details.pincode', 'LIKE', "%$search%")
                  ->orWhere('address.address', 'LIKE', "%$search%");
        }

        if ($request->filled('refernull')) {
            $refernull = $request->refernull;
            $query->where('user_details.referred_by', null);
            }
        
        // Remove a specific query parameter, e.g., 'user_id'
        $queryParameters = Arr::except($request->query(), ['user_id']);

        // Paginate the results
        $users = $query->paginate(30)->appends($queryParameters); // Adjust the number 10 to set items per page

        return response()->json($users);
    }


    public function show(Request $request)
    {
        try {
            // Retrieve the user by ID
            $user = User::findOrFail($request->user_id);

            // Retrieve user details associated with the user ID
            $userDetails = UserDetails::where('user_id', $user->id)->first();

            if (!$userDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'User details not found',
                ], 404);
            }

            // Prepare response data
            $data = [
                'user' => $user,
                'user_details' => $userDetails,
            ];

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        // Logic to create a new user
    }

    public function update(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email',
            'user_name' => 'required',
            'last_name' => 'required',
            'middle_name' => 'required',
            'phone_1' => 'required',
            'phone_2' => 'nullable',
            'aadhar_number' => 'nullable',
            'pincode' => 'required',
        ]);
       
        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Update Validation failed',
            ], 422);
        }

        // Retrieve validated data
        $validatedData = $validator->validated();
        $validatedData['user_id'] = $request->user_id;
        // Start database transaction
        DB::beginTransaction();

        try {
            // Find the user
            $user = User::findOrFail($validatedData['user_id']);

            // Update user data
            $userData = [
                'name' => $validatedData['user_name'],
                'email' => $validatedData['email'],
                'user_name' => $validatedData['user_name'],
                'last_name' => $validatedData['last_name'],
                'middle_name' => $validatedData['middle_name'],
            ];

            // Hash password if provided
            if (!empty($validatedData['password'])) {
                $userData['password'] = bcrypt($validatedData['password']);
            }

            $user->update($userData);

            // Update UserDetails
            $userDetails = UserDetails::where('user_id', $user->id)->firstOrFail();
            $detailsData = [
                'first_name' => $validatedData['user_name'], // Assuming user_name is first name
                'last_name' => $validatedData['last_name'],
                'middle_name' => $validatedData['middle_name'],
                'phone_1' => $validatedData['phone_1'],
                'phone_2' => $validatedData['phone_2'],
                'email' => $validatedData['email'],
                'aadhar_number' => $validatedData['aadhar_number'] ?? null,
                'pincode' => $validatedData['pincode'],
            ];

            $userDetails->update($detailsData);

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => [
                    'user' => $user,
                    'user_details' => $userDetails,
                ],
            ]);
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'User update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Logic to delete a user
    }

    public function changeUserState($user_id ,$state)
    {
            // Find the user by ID
            $user = User::find($user_id);
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
    
            // Update the cold_state
            $user->cold_state = $state;
            $user->save();
    
            return response()->json([
                'success' => true,
                'message' => 'User state updated successfully',
                'data' => $user
            ]);
    }

    public function updatePasswordWithOldPassword(Request $request): JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            // 'email' => 'required|email',  // Ensure email is provided and is valid
            'old_password' => 'required',  // Old password is required
            'password' => 'required|min:6',  // New password with a minimum length of 6
            'c_password' => 'required|same:password',  // Ensure new password matches the confirmation password
        ]);
        $user_id = $request->user_id;
        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors(),
                'message' => 'Password update validation failed',
            ], 422);
        }

        // Retrieve the user by email
        $user = User::where('id', $user_id)->first();

        // If user not found, return error
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Check if the old password matches the one in the database
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Old password is incorrect',
            ], 400);
        }

        // Update the user's password
        $user->password = bcrypt($request->password);  // Hash the new password
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ], 200);
    }


   // api for getting the users whose cold_state = 1 and with some filters
    public function getAllColdStateUsers(Request $request)
    {   
        Log::info("hello");
        // Get filter inputs from the request
        $name = $request->input('name');
        $pincode = $request->input('pincode');
        
        // Query the database
        $query = User::join('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('users.cold_state', 1);
        
        // Apply filters if they are provided
        if (!empty($name)) {
            $query->where('user_details.first_name', 'like', "%$name%");
        }
    
        if (!empty($pincode)) {
            $query->where('user_details.pincode', $pincode);
        }
    
        // Paginate the results
        $results = $query->paginate(10); // Adjust the pagination limit as needed
    
        // Return the results with a custom message
        return response()->json([
            'message' => 'Users successfully retrieved',
            'data' => $results
        ]);
    }
    
}
