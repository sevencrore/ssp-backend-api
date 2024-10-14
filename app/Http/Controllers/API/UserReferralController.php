<?php

namespace App\Http\Controllers\API;

use App\Models\UserReferral;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

/**
 * @OA\Schema(
 *     schema="UserReferral",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="reg_user_id", type="integer"),
 *     @OA\Property(property="referral_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class UserReferralController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/user-referrals",
     *     tags={"UserReferrals"},
     *     summary="Get all user referrals",
     *     @OA\Response(
     *         response=200,
     *         description="A list of user referrals",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserReferral"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $userReferrals = UserReferral::all();
        return response()->json(['success' => true, 'data' => $userReferrals]);
    }

    /**
     * @OA\Post(
     *     path="/api/user-referrals",
     *     tags={"UserReferrals"},
     *     summary="Store a new user referral",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reg_user_id", "referral_id"},
     *             @OA\Property(property="reg_user_id", type="integer"),
     *             @OA\Property(property="referral_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User referral created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserReferral")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'reg_user_id' => 'required|integer',
            'referral_id' => 'required|integer',
        ]);

        $userReferral = UserReferral::create($validatedData);
        return response()->json(['success' => true, 'data' => $userReferral], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user-referrals/{id}",
     *     tags={"UserReferrals"},
     *     summary="Get a specific user referral by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User referral found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserReferral")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User referral not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $userReferral = UserReferral::find($id);

        if (!$userReferral) {
            return response()->json(['success' => false, 'message' => 'User referral not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $userReferral]);
    }

    /**
     * @OA\Put(
     *     path="/api/user-referrals/{id}",
     *     tags={"UserReferrals"},
     *     summary="Update a specific user referral",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reg_user_id", type="integer"),
     *             @OA\Property(property="referral_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User referral updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserReferral")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User referral not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $userReferral = UserReferral::find($id);

        if (!$userReferral) {
            return response()->json(['success' => false, 'message' => 'User referral not found'], 404);
        }

        $validatedData = $request->validate([
            'reg_user_id' => 'sometimes|required|integer',
            'referral_id' => 'sometimes|required|integer',
        ]);

        $userReferral->update($validatedData);
        return response()->json(['success' => true, 'data' => $userReferral]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-referrals/{id}",
     *     tags={"UserReferrals"},
     *     summary="Delete a specific user referral",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User referral deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User referral not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $userReferral = UserReferral::find($id);

        if (!$userReferral) {
            return response()->json(['success' => false, 'message' => 'User referral not found'], 404);
        }

        $userReferral->delete();
        return response()->json(['success' => true, 'message' => 'User referral deleted successfully']);
    }
}
