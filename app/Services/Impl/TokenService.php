<?php

namespace App\Services\Impl;

use App\Exceptions\ApplicationException;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\TokenService as TokenInterface;
use Carbon\Carbon;

class TokenService implements TokenInterface
{
    public function __construct(private readonly RefreshToken $refreshToken)
    {
    }

    public function createAccessToken(User $user): string
    {
        return jwt_build_token($user->toArray());
    }

    public function createRefreshToken(User $user): string
    {
        $refreshToken = jwt_build_refresh_token();

        $this->refreshToken->fill([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => Carbon::now()->addDays(config('jwt.refresh_ttl'))
        ])->save();

        return $refreshToken;
    }

    /**
     * @throws ApplicationException
     */
    public function refresh(string $refreshToken): array
    {
        $storedRefreshToken = $this->refreshToken->where('token', $refreshToken)->first();

        if (
            !$storedRefreshToken
            || Carbon::now()->gt($storedRefreshToken->expires_at)
            || $storedRefreshToken->is_revoked
        ) {
            throw new ApplicationException('Invalid refresh token.');
        }

        $storedRefreshToken->is_revoked = true;
        $storedRefreshToken->save();

        $newAccessToken = $this->createAccessToken($storedRefreshToken->user);
        $newRefreshToken = $this->createRefreshToken($storedRefreshToken->user);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ];
    }
}
