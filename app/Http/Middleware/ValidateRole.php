<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->get('role');

        if (!$role || !in_array($role, User::getRoles())) {
            return response()->json(['message' => 'Invalid role.'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
