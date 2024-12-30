<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
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

}
