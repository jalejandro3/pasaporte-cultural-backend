<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateQrToken
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->get('token');

        if (!$token) {
            throw new UnauthorizedException('Token not valid.');
        }

        $secret = config('qr.secret');
        $decodeToken = base64_decode($token);
        [$payloadString, $hash] = explode('.', $decodeToken);
        $expectedHash = hash_hmac('sha256', $payloadString, $secret);
        $payload = json_decode($payloadString, true);

        if (!hash_equals($hash, $expectedHash) || !$payload || !isset($payload['activity_id'], $payload['timestamp'])) {
            throw new UnauthorizedException('Invalid QR code.');
        }

        return $next($request);
    }
}
