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

class UsersController extends BaseController
{
    public function index()
    {
        // Logic to list all users
    }

    public function show($id)
    {
        // Logic to display a specific user by ID
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
