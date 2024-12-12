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
        $email = $request->get('email');
        $allowedDomains = config('auth.allowed_domains', []);

        if (!count($allowedDomains)) {
            return response()->json(['message' => 'No valid domains were configured.'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array(extract_domain($email), $allowedDomains)) {
            return response()->json(['message' => 'Invalid data. Please try again.'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
