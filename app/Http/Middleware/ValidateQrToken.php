<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateQrToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->get('token');

        if (!$token) {
            return response()->json(['message' => 'Token not valid.'], Response::HTTP_UNAUTHORIZED);
        }

        $secret = config('qr.secret');
        $decodeToken = base64_decode($token);
        [$payloadString, $hash] = explode('.', $decodeToken);
        $expectedHash = hash_hmac('sha256', $payloadString, $secret);
        $payload = json_decode($payloadString, true);

        if (!hash_equals($hash, $expectedHash) || !$payload || !isset($payload['activity_id'], $payload['timestamp'])) {
            return response()->json(['message' => 'Invalid QR code'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
