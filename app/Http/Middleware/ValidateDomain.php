<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->only(['email']);
        $allowedDomains = explode(env('ALLOWED_DOMAINS'), ',');

        if (!in_array($email, $allowedDomains)) {
            return response()->json(['message' => 'Invalid data. Please try again'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
