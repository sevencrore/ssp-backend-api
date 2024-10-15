<?php

namespace App\Http\Controllers\API;

use App\Models\Earning;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\EarningResource;

/**
 * @OA\Schema(
 *     schema="EarningResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="referral_incentive", type="integer", example=100),
 *     @OA\Property(property="sale_value_estimated", type="integer", example=500),
 *     @OA\Property(property="sale_actual_value", type="integer", example=450),
 *     @OA\Property(property="wallet_amount", type="integer", example=50),
 *     @OA\Property(property="self_purchase_total", type="integer", example=200),
 *     @OA\Property(property="first_referral_purchase_total", type="integer", example=150),
 *     @OA\Property(property="second_referral_purchase_total", type="integer", example=100),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */
class EarningController extends BaseController
{
    // Validation method for earnings
    private function validateEarning(Request $request)
    {
        return $request->validate([
            'referral_incentive' => 'required|integer',
            'sale_value_estimated' => 'required|integer',
            'sale_actual_value' => 'required|integer',
            'wallet_amount' => 'required|integer',
            'self_purchase_total' => 'required|integer',
            'first_referral_purchase_total' => 'required|integer',
            'second_referral_purchase_total' => 'required|integer',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/earnings",
     *     tags={"Earnings"},
     *     summary="Get all earnings",
     *     description="Returns a paginated list of all earnings.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Earnings retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/EarningResource")
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=10)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $earnings = Earning::paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Earnings retrieved successfully.',
            'data' => EarningResource::collection($earnings),
            'pagination' => [
                'total' => $earnings->total(),
                'current_page' => $earnings->currentPage(),
                'last_page' => $earnings->lastPage(),
                'per_page' => $earnings->perPage(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/earnings",
     *     tags={"Earnings"},
     *     summary="Store a new earning",
     *     description="Creates a new earning record.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="referral_incentive", type="integer", example=100),
     *             @OA\Property(property="sale_value_estimated", type="integer", example=500),
     *             @OA\Property(property="sale_actual_value", type="integer", example=450),
     *             @OA\Property(property="wallet_amount", type="integer", example=50),
     *             @OA\Property(property="self_purchase_total", type="integer", example=200),
     *             @OA\Property(property="first_referral_purchase_total", type="integer", example=150),
     *             @OA\Property(property="second_referral_purchase_total", type="integer", example=100),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Earning created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Earning created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/EarningResource")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->validateEarning($request);
        $earning = Earning::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Earning created successfully.',
            'data' => new EarningResource($earning)
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/earnings/{id}",
     *     tags={"Earnings"},
     *     summary="Get a specific earning",
     *     description="Retrieves a specific earning by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Earning retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Earning retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/EarningResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Earning not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Earning not found.")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $earning = Earning::find($id);

        if (!$earning) {
            return response()->json([
                'success' => false,
                'message' => 'Earning not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Earning retrieved successfully.',
            'data' => new EarningResource($earning)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/earnings/{id}",
     *     tags={"Earnings"},
     *     summary="Update a specific earning",
     *     description="Updates a specific earning by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="referral_incentive", type="integer", example=100),
     *             @OA\Property(property="sale_value_estimated", type="integer", example=500),
     *             @OA\Property(property="sale_actual_value", type="integer", example=450),
     *             @OA\Property(property="wallet_amount", type="integer", example=50),
     *             @OA\Property(property="self_purchase_total", type="integer", example=200),
     *             @OA\Property(property="first_referral_purchase_total", type="integer", example=150),
     *             @OA\Property(property="second_referral_purchase_total", type="integer", example=100),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Earning updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Earning updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/EarningResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Earning not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Earning not found.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $earning = Earning::find($id);

        if (!$earning) {
            return response()->json([
                'success' => false,
                'message' => 'Earning not found.',
            ], 404);
        }

        $validatedData = $this->validateEarning($request);
        $earning->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Earning updated successfully.',
            'data' => new EarningResource($earning)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/earnings/{id}",
     *     tags={"Earnings"},
     *     summary="Delete a specific earning",
     *     description="Deletes a specific earning by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Earning deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Earning deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Earning not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Earning not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $earning = Earning::find($id);

        if (!$earning) {
            return response()->json([
                'success' => false,
                'message' => 'Earning not found.',
            ], 404);
        }

        $earning->delete();

        return response()->json([
            'success' => true,
            'message' => 'Earning deleted successfully.',
        ]);
    }
}
