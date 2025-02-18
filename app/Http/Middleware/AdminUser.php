<?php

namespace App\Http\Middleware;

use App\Enums\UserRoles;
use App\Exceptions\UnauthorizedException;
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
                throw new UnauthorizedException('Token not found.');
            }

            $decoded = jwt_decode_token($token);

            if (UserRoles::ADMIN->value !== $decoded->data->role) {
                throw new UnauthorizedException('Unauthorized');
            }
        } catch (Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }

        return $next($request);
    }
}
