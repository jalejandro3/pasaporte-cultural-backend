<?php

use Carbon\Carbon;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Illuminate\Validation\UnauthorizedException;

if (!function_exists('jwt_build_token')) {
    function jwt_build_token(array $data): string
    {
        $payload = [
            'iss' => ENV('JWT_ISS'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addHour()->timestamp,
            'jti' => uniqid('jwt_', true),
            'data' => $data
        ];

        return JWT::encode($payload, ENV('JWT_SECRET'), ENV('JWT_ALGORITHM'));
    }
}

if (!function_exists('jwt_decode_token')) {
    function jwt_decode_token(string $token): object
    {
        $key = new Key(ENV('JWT_SECRET'), ENV('JWT_ALGORITHM'));

        return JWT::decode($token, $key);
    }
}
