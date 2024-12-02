<?php

return [
    'algorithm' => env('JWT_ALGORITHM'),
    'issuer' => env('JWT_ISS'),
    'refresh_ttl' => 15,
    'secret' => env('JWT_SECRET'),
    'ttl' => 1,
    'jti_prefix' => 'jwt_pcu_',
];
