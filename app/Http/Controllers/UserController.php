<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\UserService as UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly User $user,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserServiceInterface $userService
    ) {
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
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            // Validar la solicitud
            $validator = Validator::make($request->all(), [
                'email' => 'nullable|email|unique:users,email',
                'password' => 'nullable|string|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'msg' => $validator->errors()->first(),
                ], 400);
            }
            // Obtener el usuario autenticado
            $user = Auth::user();
            // var_dump($user);
            // exit;
            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'msg' => 'Unauthorized. Please login first.',
                ], 401);
            }
            // Actualizar los campos si están presentes
            if ($request->filled('email')) {
                $user->email = $request->email;
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            // Guardar los cambios en la base de datos
            $user->save();
            return response()->json([
                'code' => 200,
                'msg' => 'User updated successfully.',
            ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'code' => 500,
                'msg' => 'An error occurred while updating the user.',
            ], 500);
        }
    }
}
