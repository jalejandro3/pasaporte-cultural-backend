<?php

namespace App\Http\Middleware;

use App\Exceptions\ApplicationException;
use App\Exceptions\InputValidationException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDomain
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws ApplicationException
     * @throws InputValidationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->get('email');
        $allowedDomains = config('auth.allowed_domains', []);

        if (!count($allowedDomains)) {
            throw new ApplicationException('No valid domains were configured.');
        }

        if ($email && !in_array(extract_domain($email), $allowedDomains)) {
            throw new InputValidationException('Invalid data. Please try again.');
        }

        return $next($request);
    }
}
