<?php

namespace App\Http\Controllers\API;

use App\Models\Earning;
use App\Models\User;
use App\Models\Order;
use App\Models\UserDetails;
use App\Models\ConfigSetting;
use App\Models\ComissionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'user_id' => 'required|integer',
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


    public function getEarningsByUser($id): JsonResponse
    {
        $user = User::find($id);
    
        if ($user) {
            $earnings = Earning::where('user_id', $id)->get();
            return response()->json([
                'success' => true,
                'message' => 'Earnings retrieved successfully.',
                'data' => $earnings
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
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



    public function updateEarningsWithReferralIncentive($id)
    {
        try {
            // Validate the input
            if (!$id) {
                return response()->json(['error' => 'User ID is required.'], 400);
            }

            // Start a database transaction
            DB::beginTransaction();

            // Fetch the matching earnings record
            $earning = Earning::where('user_id', $id)->first();

            if (!$earning) {
                return response()->json(['message' => 'No earnings found for this user.'], 404);
            }

            // Fetch the referral incentive from the config_setting table
            $configSetting = ConfigSetting::find(1);
            if (!$configSetting) {
                return response()->json(['error' => 'Config setting not found.'], 404);
            }

            $referralIncentive = $configSetting->referal_incentive;
            Log::info($configSetting);
           
            if ($earning->referral_incentive <= $referralIncentive){
                $earning->wallet_amount += $earning->referral_incentive; // Add referral incentive to wallet
                $earning->referral_incentive=0;
            }
            else {
                 // Update wallet_amount and adjust the previous amount
                $earning->wallet_amount += $referralIncentive; // Add referral incentive to wallet
                $earning->referral_incentive -= $referralIncentive; // Deduct referral incentive from the previous amount
            }
            // Save the updated earnings record
            $earning->save();

            // Commit the transaction
            DB::commit();

            return 'success';

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            return 'fail';
        }
    }



    public function calculateCommission($userId, $grandTotal, $level)
    {
        // Fetch the user details using user_id
        $userDetail = UserDetails::where('user_id', $userId)->first();
        if (!$userDetail) {
            return response()->json(['error' => 'User details not found'], 404);
        }
    
        $comission_id = $userDetail->comission_id;
    
        // Fetch the commission details using comission_id and level
        $comissionDetail = ComissionDetail::where('comission_id', $comission_id)
                                            ->where('level', $level)
                                            ->first();
        if (!$comissionDetail) {
            return response()->json(['error' => 'Commission details not found'], 404);
        }
    
        $comissionPercentage = $comissionDetail->commission;
    
        // Calculate the commission
        $commissionAmount = $grandTotal * ($comissionPercentage / 100);
    
        // Check if an earnings record exists for the user_id
        $earning = Earning::where('user_id', $userId)->first();
    
        if ($earning) {
            // Update the wallet amount if the record exists
            $earning->wallet_amount += $commissionAmount;
            $earning->save();
        } else {
            // Create a new earnings record if it doesn't exist
            $earningData = [
                'referral_incentive' => 0,
                'sale_value_estimated' => 0,
                'sale_actual_value' => 0,
                'wallet_amount' => $commissionAmount,
                'self_purchase_total' => 0,
                'first_referral_purchase_total' => 0,
                'second_referral_purchase_total' => 0,
                'user_id' => $userId,
            ];
            Earning::create($earningData);
        }
    
        return $userDetail->referred_by;  // Return the user that referred the current user
    }
    
    // Main function
    public function addComission($id)
    {
        // Retrieve the order ID from the request
        $orderId = $id;
    
        // Fetch the order details using the order ID
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        $userId = $order->user_id;
        $grandTotal = $order->grand_total;
    
        // Call the reusable function for level 1
        $currentUserId = $this->calculateCommission($userId, $grandTotal, 1);
        
        $max_depth = ConfigSetting::find(1)->max_level;
        // Ensure $currentUserId is valid and continue for levels 2 and 3
        for ($i = 1; $max_depth <= 2; $i++) {
            if (!$currentUserId) {
                break;  // Exit the loop if no valid user ID is returned
            }
    
            // Update userId to the referred user's ID for the next level commission
            $currentUserId = $this->calculateCommission($currentUserId, $grandTotal, $i);
        }
    
        // Return the response from the reusable function
        return response()->json(['message' => 'Commission calculated successfully'], 200);
    }
    



}