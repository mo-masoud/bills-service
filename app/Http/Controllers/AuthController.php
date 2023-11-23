<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use function auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        $token = $user->createToken('Normal')->plainTextToken;

        return response()->json(compact('user', 'token'));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (auth()->attempt($request->validated())) {
            $user = auth()->user();
            $token = $user->createToken('Normal')->plainTextToken;

            return response()->json(compact('user', 'token'));
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'success']);
    }
}
