<?php

namespace App\Http\Middleware;

use App\Enums\UserRoles;
use App\Exceptions\InputValidationException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws InputValidationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->get('role');

        if (!$role || !in_array($role, UserRoles::getValues())) {
            throw new InputValidationException('Invalid role.');
        }

        return $next($request);
    }
}
