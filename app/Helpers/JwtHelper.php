<?php

use App\Exceptions\ApplicationException;
use Carbon\Carbon;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;

if (!function_exists('jwt_build_token')) {
    /**
     * @throws ApplicationException
     */
    function jwt_build_token(array $data): string
    {
        try {
            $payload = [
                'iss' => config('jwt.issuer'),
                'iat' => Carbon::now()->timestamp,
                'exp' => Carbon::now()->addHours(config('jwt.ttl'))->timestamp,
                'jti' => uniqid(config('jwt.jti_prefix'), true),
                'data' => $data
            ];

            return JWT::encode($payload, config('jwt.secret'), config('jwt.algorithm'));
        } catch (Exception $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}

if (!function_exists('jwt_build_refresh_token')) {
    /**
     * @throws ApplicationException
     */
    function jwt_build_refresh_token(): string
    {
        try {
            $payload = [
                'iss' => config('jwt.issuer'),
                'iat' => Carbon::now()->timestamp,
                'exp' => Carbon::now()->addDays(config('jwt.refresh_ttl'))->timestamp,
                'jti' => uniqid(config('jwt.jti_prefix'), true),
            ];

            return JWT::encode($payload, config('jwt.secret'), config('jwt.algorithm'));
        } catch (Exception $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}

if (!function_exists('jwt_decode_token')) {
    /**
     * @throws ApplicationException
     */
    function jwt_decode_token(string $token): object
    {
        try {
            $key = new Key(config('jwt.secret'), config('jwt.algorithm'));

            return JWT::decode($token, $key);
        } catch (Exception $e) {
            throw new ApplicationException($e->getMessage());
        }
    }
}
