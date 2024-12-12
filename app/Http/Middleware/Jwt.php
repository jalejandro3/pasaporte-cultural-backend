<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Jwt
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['message' => 'Token not found.'], Response::HTTP_UNAUTHORIZED);
            }

            $decoded = jwt_decode_token($token);

            $request->auth = $decoded;
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
