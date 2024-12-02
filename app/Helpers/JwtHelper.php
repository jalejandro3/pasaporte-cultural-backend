<?php

use Carbon\Carbon;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

if (!function_exists('jwt_build_token')) {
    function jwt_build_token(array $data): string
    {
        $payload = [
            'iss' => config('jwt.issuer'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addHours(config('jwt.ttl'))->timestamp,
            'jti' => uniqid(config('jwt.jti_prefix'), true),
            'data' => $data
        ];

        return JWT::encode($payload, config('jwt.secret'), config('jwt.algorithm'));
    }
}

if (!function_exists('jwt_build_refresh_token')) {
    function jwt_build_refresh_token(): string
    {
        $payload = [
            'iss' => config('jwt.issuer'),
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addDays(config('jwt.refresh_ttl'))->timestamp,
            'jti' => uniqid(config('jwt.jti_prefix'), true),
        ];

        return JWT::encode($payload, config('jwt.secret'), config('jwt.algorithm'));
    }
}

if (!function_exists('jwt_decode_token')) {
    function jwt_decode_token(string $token): object
    {
        $key = new Key(config('jwt.secret'), config('jwt.algorithm'));

        return JWT::decode($token, $key);
    }
}
