<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Get user_id from PersonalAccessToken using token and merge user_id to $request
        $token = $request->bearerToken();
        if($token) {
            $personalAccessToken = PersonalAccessToken::findToken($token);
            if($personalAccessToken) {
                $request->merge(['user_id' => $personalAccessToken->tokenable_id]);
            }
        }
        
        
        $response = $next($request);

        // Set CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        // // Handle preflight requests
        // if ($request->isMethod('OPTIONS')) {
        //     return response()->json([], 200);
        // }

        return $response;
    }


    // public function handle(Request $request, Closure $next): Response
    // {
    //     // return $next($request);

    //     $response = $next($request);

    //     // Set CORS headers
    //     $response->headers->set('Access-Control-Allow-Origin', '*'); // Change this for production
    //     $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    //     $response->headers->set('Access-Control-Allow-Credentials', 'true');

    //     // Handle preflight requests
    //     if ($request->isMethod('OPTIONS')) {
    //         return response()->json([], 200);
    //     }

    //     return $response;
    // }
}
