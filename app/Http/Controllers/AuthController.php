<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        $currentUser = $request->user();

        // If authenticated as guest, convert this account to full user (keeps their data)
        if ($currentUser?->isGuest()) {
            $currentUser->update([
                'email'    => $request->validated('email'),
                'password' => Hash::make($request->password),
                'is_guest' => false,
            ]);
            $user = $currentUser->fresh();
            $request->user()?->currentAccessToken()?->delete();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'user'  => $user->only(['id', 'email', 'created_at', 'is_guest']),
                'token' => $token,
            ], Response::HTTP_OK);
        }

        $user = User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id', 'email', 'created_at', 'is_guest']),
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

        if ($user->isGuest()) {
            throw ValidationException::withMessages([
                'email' => __('Guest accounts cannot sign in with email and password. Create an account to continue.'),
            ]);
        }

        $tokenName = $request->boolean('remember')
            ? 'remember-token'
            : 'auth-token';

        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id', 'email', 'created_at', 'is_guest']),
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: '/auth/guest',
        summary: 'Create guest session',
        description: 'Creates a temporary guest user and returns a Bearer token. Free-tier users can use the app and their data is stored under this guest user. Use register (with this token in Authorization header) to convert to a full account and keep data.',
        tags: ['Authentication'],
    )]
    #[OA\Response(
        response: 201,
        description: 'Guest session created',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'user', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'email', type: 'string', example: 'guest_01ARZ3NDEKTSV4RRFFQ69G5FAV@guest.local'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'is_guest', type: 'boolean', example: true),
                ]),
                new OA\Property(property: 'token', type: 'string', example: '3|guest_token_xyz'),
            ],
        ),
    )]
    public function guest(Request $request): JsonResponse
    {
        $user = User::create([
            'email'    => 'guest_' . Str::ulid() . '@guest.local',
            'password' => Hash::make(Str::random(32)),
            'is_guest' => true,
        ]);

        $token = $user->createToken('guest-token')->plainTextToken;

        return response()->json([
            'user'  => $user->only(['id', 'email', 'created_at', 'is_guest']),
            'token' => $token,
        ], Response::HTTP_CREATED);
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
                new OA\Property(property: 'is_guest', type: 'boolean', example: false),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(
            $user ? $user->only(['id', 'email', 'created_at', 'is_guest']) : ['message' => 'Unauthenticated'],
            $user ? 200 : 401
        );
    }
}
