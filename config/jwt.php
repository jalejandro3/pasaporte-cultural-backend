<?php

return [
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),
    'issuer' => env('JWT_ISS', ''),
    'refresh_ttl' => 15,
    'secret' => env('JWT_SECRET', 'secret'),
    'ttl' => 1,
    'jti_prefix' => 'jwt_pcu_',
];
