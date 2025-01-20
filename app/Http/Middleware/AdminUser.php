<?php

namespace App\Http\Middleware;

use App\Enums\UserRoles;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUser
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

            if (UserRoles::ADMIN !== $decoded->data->role) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
