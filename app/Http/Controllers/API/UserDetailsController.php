<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserDetails;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="UserDetailsResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="middle_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="phone_1", type="string"),
 *     @OA\Property(property="phone_2", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class UserDetailsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-details",
     *     tags={"UserDetails"},
     *     summary="Get all user details",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserReferral"))
     *         )
     *     )
     *  )
     */


    // Get all user details
    public function index(): JsonResponse
    {
        $userDetails = UserDetails::all(); // Fetch all user details

        return response()->json(['success' => true, 'data' => $userDetails]);
    }

/**
 * @OA\Post(
 *     path="/api/user-details",
 *     tags={"UserDetails"},
 *     summary="Store a new user detail",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string"),
 *             @OA\Property(property="middle_name", type="string", nullable=true),
 *             @OA\Property(property="last_name", type="string"),
 *             @OA\Property(property="phone_1", type="string"),
 *             @OA\Property(property="phone_2", type="string", nullable=true),
 *             @OA\Property(property="email", type="string", format="email"),
 *         
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User detail created",
 *         @OA\JsonContent(
 *              @OA\Property(property="success", type="boolean"),
 *              @OA\Property(property="data", type="object",
 *              @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="first_name", type="string", example="New Category"),
 *                 @OA\Property(property="middle_name", type="string", example="Category Description"),
 *                 @OA\Property(property="last_name", type="string", example="Category Description"),
 *                 @OA\Property(property="phone_1", type="string", example="Category Description"),
 *                 @OA\Property(property="phone_2", type="string", example="Category Description")
 *              )
 *             )
 *          )
 *      ),
 *     @OA\Response(response=400, description="Validation error")
 * )
 */

    // Store a new user detail
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_1' => 'required|string|max:15',
            'phone_2' => 'nullable|string|max:15',
            'email' => 'required|string|email|max:255|unique:user_details,email',
        ]);

        $userDetail = UserDetails::create($validatedData); // Create a new user detail

        return response()->json(['success' => true, 'data' => $userDetail], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user-details/{id}",
     *     tags={"UserDetails"},
     *     summary="Get a specific user detail by ID",
     *     description="Returns details of a specific city.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="first_name", type="string", example="New Category"),
     *                 @OA\Property(property="middle_name", type="string", example="Category Description"),
     *                 @OA\Property(property="last_name", type="string", example="Category Description"),
     *                 @OA\Property(property="phone_1", type="string", example="Category Description"),
     *                 @OA\Property(property="phone_2", type="string", example="Category Description")
     *          )
     * )
     *     ),
     *     @OA\Response(response=404, description="User detail not found"),
     * )
     */

    // Get a specific user detail by ID
    public function show($id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $userDetail]);
    }

    /**
     * @OA\Put(
     *     path="/api/user-details/{id}",
     *     tags={"UserDetails"},
     *     summary="Update a specific user detail",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="middle_name", type="string", nullable=true),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="phone_1", type="string"),
     *             @OA\Property(property="phone_2", type="string", nullable=true),
     *             @OA\Property(property="email", type="string", format="email"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User detail updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="first_name", type="string", example="New Category"),
     *                 @OA\Property(property="middle_name", type="string", example="Category Description"),
     *                 @OA\Property(property="last_name", type="string", example="Category Description"),
     *                 @OA\Property(property="phone_1", type="string", example="Category Description"),
     *                 @OA\Property(property="phone_2", type="string", example="Category Description")
     *    )
     *  ),
     *     @OA\Response(response=404, description="User detail not found"),
     * )
     * )
     */


    // Update a specific user detail
    public function update(Request $request, $id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        $validatedData = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone_1' => 'sometimes|required|string|max:15',
            'phone_2' => 'sometimes|nullable|string|max:15',
            'email' => 'sometimes|required|string|email|max:255|unique:user_details,email,' . $userDetail->id,
        ]);

        $userDetail->update($validatedData); // Update the user detail

        return response()->json(['success' => true, 'data' => $userDetail]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-details/{id}",
     *     tags={"UserDetails"},
     *     summary="Delete a specific user detail",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=204,
     *         description="User detail deleted successfully",
     *     ),
     *     @OA\Response(response=404, description="User detail not found"),
     * )
     */

    // Delete a specific user detail
    public function destroy($id): JsonResponse
    {
        $userDetail = UserDetails::find($id);

        if (!$userDetail) {
            return response()->json(['success' => false, 'message' => 'User detail not found'], 404);
        }

        $userDetail->delete(); // Delete the user detail

        return response()->json(['success' => true, 'message' => 'User detail deleted successfully']);
    }
}
