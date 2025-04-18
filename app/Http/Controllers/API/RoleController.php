<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function getAllRoles()
    {
        $roles = Role::select('id', 'name')->get(); // Fetch all roles with ID & name

        return response()->json([
            'message' => 'Roles fetched successfully',
            'roles' => $roles,
        ]);
    }

    
    public function updateRole(Request $request)
    {
        try {
            // Validate request input
            $validatedData = $request->validate([
                'userId' => 'required|exists:users,id', // Ensure user exists
                'role_id' => 'required|exists:roles,id', // Ensure role exists
            ]);
            
            DB::beginTransaction(); // Start transaction

            // Find the user by phone number
            $user = User::where('id', $validatedData['userId'])->firstOrFail();
    
            // Find the role by ID
            $role = Role::findOrFail($validatedData['role_id']);
    
            // Check if user already has this role
            if ($user->hasRole($role->name)) {
                return response()->json([
                    'message' => "User '{$user->user_name}' already has the '{$role->name}' role",
                ], 400);
            }
    
            // Assign the new role (replacing old roles)
            $user->syncRoles([$role->name]); // Ensures only one role at a time

            // update the user_type 
            $userController = new UsersController();
             // get the user_type by the role name
            $user_type = $userController->getUserTypeByRole($role->name);
            if(!$user_type){
                throw new \Exception('User_type assignment failed failed');  
            }
            $user->user_type = $user_type;
            $user->save();

            DB::commit(); // Commit transaction
    
            // Return a success response with user & role details
            return response()->json([
                'message' => "Role '{$role->name}' assigned successfully to user '{$user->user_name}'",
                'user' => [
                    'id' => $user->id,
                    'name' => $user->user_name,
                    'email' => $user->email,
                    
                ],
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                ],
            ], 200);
    
        } catch (ValidationException $e) {
            DB::rollBack(); // Rollback transaction on validation error
            // Handle validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            DB::rollBack(); // Rollback transaction on validation error
            // Handle not found errors
            return response()->json([
                'message' => "User or role not found ",
            ], 404);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback transaction on validation error
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred while updating the role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
