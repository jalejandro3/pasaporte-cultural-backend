<?php

namespace App\Http\Middleware;

use Closure;
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
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 401);
        }

        try {
            $decoded = jwt_decode_token($token);

            $request->auth = $decoded;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token', 'error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
