<?php

namespace App\Http\Controllers;

use App\Exceptions\InputValidationException;
use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\UserService as UserServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

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
            throw new ResourceNotFoundException('User not found.');
        }

        $this->user->destroy($id);

        return $this->success(['message' => 'User deleted successfully.']);
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success($this->userService->getProfile($request->bearerToken()));
    }

    public function updateRole(int $id, Request $request): JsonResponse
    {
        return $this->success($this->userService->updateRole($id, $request->all()));
    }

    /**
     * @throws InputValidationException
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $rules = [
            'email' => 'email',
            'password' => Password::min(8)->mixedCase()->numbers(),
            'repeat_password' => 'same:password',
        ];

        $messages = [
            'repeat_password.same' => 'The password must match.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->userService->updateProfile($request->bearerToken(), $request->all()));
    }

    /**
     * @throws InputValidationException
     */
    public function getAllUsers(Request $request): Paginator
    {
        $rules = [
            'email' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string',
            'sort_order' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        $filters = $request->only('email', 'first_name', 'last_name');
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'first_name');
        $sortOrder = $request->input('sort_order', 'asc');

        return $this->userService->getAllUsers($filters, $perPage, $sortBy, $sortOrder);
    }
}
