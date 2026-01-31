<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id', 'email', 'created_at']),
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }


        $tokenName = $request->boolean('remember')
            ? 'remember-token'
            : 'auth-token';

        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id', 'email', 'created_at']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Already logged out or invalid token',
            ], 401);
        }

        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()?->only(['id', 'email', 'created_at']) ?? ['message' => 'Unauthenticated'],
            $request->user() ? 200 : 401
        );
    }
}
