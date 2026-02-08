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
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/auth/register',
        summary: 'Register a new user',
        description: 'Creates a new user account and returns an authentication token.',
        tags: ['Authentication'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'SecurePass123!'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'SecurePass123!'),
            ],
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'User registered successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'user', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                ]),
                new OA\Property(property: 'token', type: 'string', example: '1|abc123tokenstring'),
            ],
        ),
    )]
    #[OA\Response(response: 422, description: 'Validation error')]
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

    #[OA\Post(
        path: '/auth/login',
        summary: 'Login',
        description: 'Authenticates a user and returns a Bearer token.',
        tags: ['Authentication'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'SecurePass123!'),
                new OA\Property(property: 'remember', type: 'boolean', example: false, description: 'Issue a long-lived remember token'),
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Login successful',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'user', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                ]),
                new OA\Property(property: 'token', type: 'string', example: '2|xyz789tokenstring'),
            ],
        ),
    )]
    #[OA\Response(response: 422, description: 'Invalid credentials')]
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

    #[OA\Post(
        path: '/auth/logout',
        summary: 'Logout',
        description: 'Revokes the current access token.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Logged out successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Successfully logged out'),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
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

    #[OA\Get(
        path: '/auth/me',
        summary: 'Current user',
        description: 'Returns the currently authenticated user.',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Current user details',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function me(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()?->only(['id', 'email', 'created_at']) ?? ['message' => 'Unauthenticated'],
            $request->user() ? 200 : 401
        );
    }
}
