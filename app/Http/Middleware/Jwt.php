<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
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
                throw new UnauthorizedException('Token not found.');
            }

            $decoded = jwt_decode_token($token);

            $request->auth = $decoded;
        } catch (Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }

        return $next($request);
    }
}
