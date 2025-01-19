<?php

namespace App\Services\Impl;

use App\Exceptions\ApplicationException;
use App\Models\PasswordResetToken;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\AuthService as AuthServiceInterface;
use App\Services\TokenService as TokenServiceInterface;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly TokenServiceInterface $tokenService,
        private readonly UserRepositoryInterface $userRepository
    )
    {
    }

    public function forgotPassword(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new ApplicationException('Please verify your email.', Response::HTTP_NOT_FOUND);
        }

        $token = Str::random(64);

        PasswordResetToken::updateOrInsert(
            [
                'email' => $email,
            ],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        $resetLink = config('app.frontend_url') . "/auth/reset-password?token=$token";

        try {
            Mail::send('emails.password-reset', ['resetLink' => $resetLink], function ($message) use ($email) {
                $message->to($email)->subject('Recuperar Contraseña');
            });

            return ['message' => 'Password reset link sent to your email.'];
        } catch (Exception $e) {
            Log::error("Failed to send password reset link to $email");
            Log::error($e->getMessage());

            return ['message' => 'Failed to send password reset link.'];
        }
    }

    public function login(string $email, string $password): array
    {
        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new ApplicationException('Wrong email or password, please verify your data.', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findByEmail($email);
        $accessToken = $this->tokenService->createAccessToken($user);
        $refreshToken = $this->tokenService->createRefreshToken($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user->toArray()
        ];
    }


    public function register(array $userData): array
    {
        $this->emailExists($userData['email']);

        $this->idDocumentExists($userData['id_document']);

        $userData['password'] = Hash::make($userData['password']);

        $this->userRepository->create($userData);

        return ['message' => 'User created successfully.'];
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        $storedToken = PasswordResetToken::where('token', $token)->first();

        $this->invalidToken($storedToken);
        $this->expiredToken($storedToken);

        $user = $this->userRepository->findByEmail($storedToken->email);

        if (!$user) {
            throw new ApplicationException('User not found.', Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $storedToken->delete();

        return ['message' => 'Password updated successfully.'];
    }

    public function validateToken(string $token): array
    {
        $storedToken = PasswordResetToken::where('token', $token)->first();

        $this->invalidToken($storedToken);
        $this->expiredToken($storedToken);

        return ['message' => 'Token is valid.'];
    }

    /**
     * @throws ApplicationException
     */
    private function emailExists(string $email): void
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new ApplicationException('The email already exists, please use a new one.');
        }
    }

    /**
     * @throws ApplicationException
     */
    private function expiredToken(PasswordResetToken $token): void
    {
        if (Carbon::parse($token->created_at)->addHour()->isPast()) {
            throw new ApplicationException('Token expired.', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws ApplicationException
     */
    private function idDocumentExists(string $idDocument): void
    {
        if ($this->userRepository->findByIdDocument($idDocument)) {
            throw new ApplicationException('The id document already exists.');
        }
    }

    /**
     * @throws ApplicationException
     */
    private function invalidToken(?PasswordResetToken $token): void
    {
        if (!$token) {
            throw new ApplicationException('Invalid token.', Response::HTTP_BAD_REQUEST);
        }
    }
}
