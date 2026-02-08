<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use OpenApi\Attributes as OA;

class PasswordController extends Controller
{
    #[OA\Post(
        path: '/auth/forgot-password',
        summary: 'Request password reset link',
        description: 'Sends a password reset link to the given email address.',
        tags: ['Password'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Reset link sent',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'We have emailed your password reset link.'),
            ],
        ),
    )]
    #[OA\Response(response: 422, description: 'Validation error or unable to send reset link')]
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[OA\Post(
        path: '/auth/reset-password',
        summary: 'Reset password',
        description: 'Resets the user password using the token received by email. All existing tokens are revoked after a successful reset.',
        tags: ['Password'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['token', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'reset-token-from-email'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'NewSecurePass123!'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'NewSecurePass123!'),
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Password reset successful',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Your password has been reset.'),
            ],
        ),
    )]
    #[OA\Response(response: 422, description: 'Validation error or invalid/expired token')]
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Revoke all tokens after password reset (strong security practice)
                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
