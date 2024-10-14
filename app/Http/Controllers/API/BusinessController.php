<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BusinessResource; // Ensure this is included
use Illuminate\Http\JsonResponse;

class BusinessController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/business",
     *     tags={"Business"},
     *     summary="Get authenticated business",
     *     description="Returns the authenticated business details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Business Name"),
     *             @OA\Property(property="email", type="string", example="business@example.com")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(): JsonResponse
    {
        $items = Business::paginate(2)->appends(['sort' => 'name']);

        $data = [
            'data' => BusinessResource::collection($items->items()), // Format the items
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'next_page_url' => $items->nextPageUrl(),
                'prev_page_url' => $items->previousPageUrl()
            ]
        ];

        return $this->sendResponse($data, 'Business retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/business",
     *     tags={"Business"},
     *     summary="Create a new business",
     *     description="Creates a new business entry",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"business_name", "address", "city", "postal_code", "phone_number", "website", "description", "keywords", "is_approved"},
     *             @OA\Property(property="business_name", type="string", example="My Business"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="city", type="string", example="My City"),
     *             @OA\Property(property="postal_code", type="string", example="12345"),
     *             @OA\Property(property="phone_number", type="string", example="555-5555"),
     *             @OA\Property(property="website", type="string", example="http://business.com"),
     *             @OA\Property(property="description", type="string", example="A detailed description"),
     *             @OA\Property(property="keywords", type="string", example="keyword1, keyword2"),
     *             @OA\Property(property="is_approved", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Business created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'business_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'website' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'is_approved' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $business = Business::create($input);

        return $this->sendResponse(new BusinessResource($business), 'Business created successfully.');
    } 

    /**
     * @OA\Get(
     *     path="/api/business/{id}",
     *     tags={"Business"},
     *     summary="Get a specific business by ID",
     *     description="Returns details of a specific business",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Business found"),
     *     @OA\Response(response=404, description="Business not found")
     * )
     */
    public function show($id): JsonResponse
    {
        $business = Business::find($id);

        if (is_null($business)) {
            return $this->sendError('Business not found.');
        }

        return $this->sendResponse(new BusinessResource($business), 'Business retrieved successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/business/{id}",
     *     tags={"Business"},
     *     summary="Update a specific business",
     *     description="Updates a business record",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")F
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"business_name", "address", "city", "postal_code", "phone_number", "website", "description", "keywords", "is_approved"},
     *             @OA\Property(property="business_name", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="postal_code", type="string"),
     *             @OA\Property(property="phone_number", type="string"),
     *             @OA\Property(property="website", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="keywords", type="string"),
     *             @OA\Property(property="is_approved", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Business updated successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Business $business): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'business_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'required',
            'website' => 'required',
            'description' => 'required',
            'keywords' => 'required',
            'is_approved' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $business->update($input);

        return $this->sendResponse(new BusinessResource($business), 'Business updated successfully.');
    }
     /**
     * @OA\Get(
     *     path="/api/business/{id}/edit",
     *     tags={"Business"},
     *     summary="Retrieve business details for editing",
     *     description="Get business details by ID for editing purposes",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the business to be edited",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/BusinessResource"),
     *             @OA\Property(property="message", type="string", example="Business data retrieved successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Business not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Business not found."),
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function edit($id): JsonResponse
        {
            $business = Business::find($id);

            if (is_null($business)) {
                return $this->sendError('Business not found.');
            }

            return $this->sendResponse(new BusinessResource($business), 'Business data retrieved successfully.');
        }




        /**
     * @OA\Post(
     *     path="/api/business/delete-multiple",
     *     tags={"Business"},
     *     summary="Delete multiple businesses",
     *     description="Soft delete multiple businesses by IDs",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"), example={1,2,3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Businesses deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="null", nullable=true),
     *             @OA\Property(property="message", type="string", example="3 businesses deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid IDs provided"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
            
        }
    
        $count = Business::whereIn('id', $ids)->delete(); // Soft deletes the records

        return $this->sendResponse(null, "{$count} businesses deleted successfully.");
    }


        /**
     * @OA\Post(
     *     path="/api/business/restore-multiple",
     *     tags={"Business"},
     *     summary="Restore multiple businesses",
     *     description="Restore multiple soft-deleted businesses by IDs",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"), example={1,2,3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Businesses restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="null", nullable=true),
     *             @OA\Property(property="message", type="string", example="3 businesses restored successfully.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid IDs provided"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function restoreMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Business::withTrashed()->whereIn('id', $ids)->restore(); // Restores the soft-deleted records

        return $this->sendResponse(null, "{$count} businesses restored successfully.");
    }


     /**
     * @OA\Post(
     *     path="/api/business/force-delete-multiple",
     *     tags={"Business"},
     *     summary="Permanently delete multiple businesses",
     *     description="Permanently delete multiple soft-deleted businesses by IDs",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"), example={1,2,3})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Businesses permanently deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="null", nullable=true),
     *             @OA\Property(property="message", type="string", example="3 businesses permanently deleted.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid IDs provided"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function forceDeleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $count = Business::withTrashed()->whereIn('id', $ids)->forceDelete(); // Permanently deletes the records

        return $this->sendResponse(null, "{$count} businesses permanently deleted.");
    }

    
    /**
 * @OA\Post(
 *     path="/api/businesses/trashed",
 *     tags={"Business"},
 *     summary="Retrieve trashed businesses",
 *     description="Returns the list of trashed businesses",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"ids"},
 *             @OA\Property(property="ids", type="array", @OA\Items(type="integer"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of trashed businesses",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/BusinessResource")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid IDs provided"),
 * )
 */

    public function trashedMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->sendError('Invalid IDs provided.');
        }

        $trashedBusinesses = Business::onlyTrashed()->whereIn('id', $ids)->get(); // Retrieves only specified soft-deleted records

        return $this->sendResponse($trashedBusinesses, 'Trashed businesses retrieved successfully.');
    }
}
