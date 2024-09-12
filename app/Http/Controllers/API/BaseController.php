<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

/**
 * @OA\Info(title="API Documentation", version="1.0.0")
 */
class BaseController extends Controller
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Successful response",
     *     @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object"),
     *         @OA\Property(property="message", type="string", example="Success message")
     *     )
     * )
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * @OA\Response(
     *     response=404,
     *     description="Error response",
     *     @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=false),
     *         @OA\Property(property="message", type="string", example="Error message"),
     *         @OA\Property(property="data", type="object", nullable=true)
     *     )
     * )
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
