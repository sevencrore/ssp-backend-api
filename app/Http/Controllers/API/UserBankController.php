<?php

namespace App\Http\Controllers\API;

use App\Models\UserBank;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;


class UserBankController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/user-bank",
     *     tags={"User Bank"},
     *     summary="Get all user banks",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User banks retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $userBanks = UserBank::paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'User banks retrieved successfully.',
            'data' => $userBanks->items(),
            'pagination' => [
                'total' => $userBanks->total(),
                'current_page' => $userBanks->currentPage(),
                'last_page' => $userBanks->lastPage(),
                'per_page' => $userBanks->perPage(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user-bank",
     *     tags={"User Bank"},
     *     summary="Store a new user bank",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"bank_name", "account_number", "ifsc_code", "branch_name"},
     *             @OA\Property(property="bank_name", type="string"),
     *             @OA\Property(property="account_number", type="string"),
     *             @OA\Property(property="ifsc_code", type="string"),
     *             @OA\Property(property="branch_name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User bank created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'a_c_holder_name' => 'required|string',
            'account_number' => 'required|string',
            'bank_name' => 'required|string',
            'branch_name' => 'required|string',
            'ifsc_code' => 'required|string',
            'phone_number' => 'required|string',
            'pancard' => 'required|string',
            'aadharcard' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $data['user_id'] = $request->user_id;

        $bank = UserBank::create($data);

        return response()->json(['message' => 'Bank details saved successfully!', 'data' => $bank], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/user-bank/{id}",
     *     tags={"User Bank"},
     *     summary="Get a specific user bank by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user bank",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User bank retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User bank not found"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $userBank = UserBank::find($id);

        if (!$userBank) {
            return response()->json([
                'success' => false,
                'message' => 'User bank not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User bank retrieved successfully.',
            'data' => $userBank,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/user-bank/{id}",
     *     tags={"User Bank"},
     *     summary="Update a specific user bank",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user bank",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="bank_name", type="string"),
     *             @OA\Property(property="account_number", type="string"),
     *             @OA\Property(property="ifsc_code", type="string"),
     *             @OA\Property(property="branch_name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User bank updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User bank not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $userBank = UserBank::find($id);

        if (!$userBank) {
            return response()->json([
                'success' => false,
                'message' => 'User bank not found.',
            ], 404);
        }

        $validatedData = $request->validate([
            'bank_name' => 'sometimes|required|string',
            'account_number' => 'sometimes|required|string|unique:user_bank,account_number,' . $userBank->id,
            'ifsc_code' => 'sometimes|required|string',
            'branch_name' => 'sometimes|required|string',
            'a_c_holder_name' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'pancard' => 'sometimes|string',
            'aadharcard' => 'sometimes|string',
        ]);

        $userBank->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'User bank updated successfully.',
            'data' => $userBank,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user-bank/{id}",
     *     tags={"User Bank"},
     *     summary="Delete a specific user bank",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user bank",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User bank deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User bank not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $userBank = UserBank::find($id);

        if (!$userBank) {
            return response()->json([
                'success' => false,
                'message' => 'User bank not found.',
            ], 404);
        }

        $userBank->delete();

        return response()->json([
            'success' => true,
            'message' => 'User bank deleted successfully.',
        ]);
    }
}
