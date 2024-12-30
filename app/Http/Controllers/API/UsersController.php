<?php

namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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

    public function update(Request $request, $id)
    {
        // Logic to update an existing user
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
            $query->where('users.business_name', 'like', "%$name%");
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
