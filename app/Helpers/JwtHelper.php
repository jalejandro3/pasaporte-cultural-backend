<?php

use Carbon\Carbon;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Illuminate\Validation\UnauthorizedException;

if (!function_exists('jwt_build_token')) {
    function jwt_build_token(array $data): string
    {
        $payload = [
            "iss" => ENV('JWT_ISS'),
            "iat" => Carbon::now()->timestamp,
            "exp" => Carbon::now()->addHour()->timestamp,
            "data" => $data
        ];

        return JWT::encode($payload, ENV('JWT_SECRET'), ENV('JWT_ALGO'));
    }
}

if (!function_exists('jwt_decode_token')) {
    function jwt_decode_token(string $token): object
    {
        try {
            $key = new Key(ENV('JWT_SECRET'), ENV('JWT_ALGO'));
            return JWT::decode($token, $key);
        } catch(Exception $e) {
            throw new UnauthorizedException($e->getMessage());
        }
    }
}
