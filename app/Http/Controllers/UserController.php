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
    ) {}

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
    public function updateProfile(Request $request): JsonResponse
    {
        try {

            $validarDatos = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/', // Requiere al menos una letra y un número
                ],
            ]);

            $this->userService->updateProfile($request->bearerToken(), $validarDatos);
            return response()->json([
                'code' => '200',
                'msg' => 'User updated successfully.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'code' => '400',
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'message' => 'Error al actualizar el perfil',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
