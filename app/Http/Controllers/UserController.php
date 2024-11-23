<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\UserService as UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly User $user,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserServiceInterface $userService
    )
    {
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->userRepository->findById($id)) {
            return $this->error('User not found.', Response::HTTP_NOT_FOUND);
        }

        $this->user->destroy($id);

        return $this->success(['message' => 'User deleted successfully.']);
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success($this->userService->getProfile($request->bearerToken()));
    }

    public function update(int $id, Request $request): JsonResponse
    {
        if (!$this->userRepository->findById($id)) {
            return $this->error('User not found.', Response::HTTP_NOT_FOUND);
        }

        $user = $this->userRepository->findById($id);
        $role = $request->get('role');

        if ($role !== $user->role) {
            $user->role = $role;

            $user->save();
        }

        return $this->success(['message' => 'User updated successfully.']);
    }
}
