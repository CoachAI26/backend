<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\PracticeSessionResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/profile',
        summary: 'Get current user profile',
        description: 'Returns the authenticated user\'s profile including computed statistics (sessions count, total minutes, average score) and achievements.',
        security: [['sanctum' => []]],
        tags: ['Profile'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Profile with statistics and achievements',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'bio', type: 'string', nullable: true, example: 'Aspiring public speaker'),
                    new OA\Property(property: 'profile_picture', type: 'string', nullable: true, example: '/storage/profile_pictures/abc123.jpg'),
                    new OA\Property(property: 'speaking_goals', type: 'array', items: new OA\Items(type: 'string'), example: ['build_confidence', 'improve_clarity']),
                    new OA\Property(property: 'notification_preferences', type: 'object', properties: [
                        new OA\Property(property: 'daily_reminder', type: 'boolean', example: true),
                        new OA\Property(property: 'weekly_report', type: 'boolean', example: false),
                    ]),
                    new OA\Property(property: 'statistics', type: 'object', properties: [
                        new OA\Property(property: 'sessions', type: 'integer', example: 12),
                        new OA\Property(property: 'minutes', type: 'integer', example: 45),
                        new OA\Property(property: 'avg_score', type: 'number', format: 'float', example: 7.8),
                    ]),
                    new OA\Property(property: 'achievements', type: 'object', properties: [
                        new OA\Property(property: 'first_session', type: 'boolean', example: true),
                        new OA\Property(property: 'five_sessions', type: 'boolean', example: true),
                        new OA\Property(property: 'ten_sessions', type: 'boolean', example: true),
                        new OA\Property(property: 'pro_speaker', type: 'boolean', example: false),
                    ]),
                ]),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function show(Request $request): ProfileResource
    {
        $user = $request->user()->load(['practiceSessions.result']);

        // Calculate statistics
        $sessionsCount = $user->practiceSessions->count();
        $totalMinutes = $user->practiceSessions->sum(function ($session) {
            return $session->completed_at ? $session->completed_at->diffInMinutes($session->started_at) : 0;
        });
        $avgScore = $user->practiceSessions->avg('result.score') ?? 0;

        // Achievements logic (simple on-the-fly)
        $achievements = [
            'first_session' => $sessionsCount >= 1,
            'five_sessions' => $sessionsCount >= 5,
            'ten_sessions' => $sessionsCount >= 10,
            'pro_speaker' => $avgScore >= 8 && $sessionsCount >= 20, // example criteria
        ];

        // Attach to user for resource
        $user->setAttribute('statistics', [
            'sessions' => $sessionsCount,
            'minutes' => $totalMinutes,
            'avg_score' => round($avgScore, 1),
        ]);
        $user->setAttribute('achievements', $achievements);

        return new ProfileResource($user);
    }

    #[OA\Post(
        path: '/profile',
        summary: 'Update profile',
        description: 'Updates the authenticated user\'s profile. Supports partial updates — only send the fields you want to change. Profile picture is uploaded as multipart form data.',
        security: [['sanctum' => []]],
        tags: ['Profile'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'John Doe'),
                    new OA\Property(property: 'bio', type: 'string', maxLength: 500, example: 'Aspiring public speaker'),
                    new OA\Property(property: 'profile_picture', type: 'string', format: 'binary', description: 'Image file (JPEG, PNG, JPG). Max 2 MB.'),
                    new OA\Property(property: 'speaking_goals', type: 'array', items: new OA\Items(type: 'string'), example: ['build_confidence', 'improve_clarity']),
                    new OA\Property(property: 'notification_preferences[daily_reminder]', type: 'boolean', example: true),
                    new OA\Property(property: 'notification_preferences[weekly_report]', type: 'boolean', example: false),
                ],
            ),
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Profile updated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', type: 'object', properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'bio', type: 'string', example: 'Aspiring public speaker'),
                    new OA\Property(property: 'profile_picture', type: 'string', example: '/storage/profile_pictures/abc123.jpg'),
                    new OA\Property(property: 'speaking_goals', type: 'array', items: new OA\Items(type: 'string')),
                    new OA\Property(property: 'notification_preferences', type: 'object'),
                ]),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validated();

        if ($request->hasFile('profile_picture')) {
            // Delete old if exists
            if ($user->profile_picture) {
                Storage::delete(str_replace('/storage/', 'public/', $user->profile_picture));
            }
            $path = $request->file('profile_picture')->store('public/profile_pictures');
            $data['profile_picture'] = Storage::url($path);
        }

        $user->update($data);

        return response()->json(new ProfileResource($user));
    }

    #[OA\Get(
        path: '/profile/history',
        summary: 'Get practice history',
        description: 'Returns all practice sessions for the authenticated user with their challenges and results, ordered by most recent first.',
        security: [['sanctum' => []]],
        tags: ['Profile'],
    )]
    #[OA\Response(
        response: 200,
        description: 'List of practice sessions',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PracticeSession')),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function history(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $sessions = $request->user()
            ->practiceSessions()
            ->with(['challenge.category', 'challenge.level', 'result'])
            ->latest()
            ->get();

        return PracticeSessionResource::collection($sessions);
    }

    #[OA\Get(
        path: '/profile/share-progress',
        summary: 'Get shareable progress summary',
        description: 'Returns a text summary of the user\'s progress suitable for sharing on social media or messaging.',
        security: [['sanctum' => []]],
        tags: ['Profile'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Shareable progress summary',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'summary',
                    type: 'string',
                    example: "John's SpeechFlow Progress:\nSessions: 12\nTotal Minutes: 45\nAvg Score: 7.8",
                ),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function shareProgress(Request $request): JsonResponse
    {
        $user = $request->user();

        // Simple summary text - could generate image/PDF in future
        $summary = sprintf(
            "%s's SpeechFlow Progress:\nSessions: %d\nTotal Minutes: %d\nAvg Score: %.1f",
            $user->name,
            $user->practiceSessions->count(),
            $user->practiceSessions->sum(fn($s) => $s->completed_at ? $s->completed_at->diffInMinutes($s->started_at) : 0),
            $user->practiceSessions->avg('result.score') ?? 0
        );

        // Could integrate with social share or generate link
        return response()->json(['summary' => $summary]);
    }

    #[OA\Delete(
        path: '/profile',
        summary: 'Delete account',
        description: 'Permanently deletes the authenticated user\'s account after verifying their current password. All tokens are revoked immediately.',
        security: [['sanctum' => []]],
        tags: ['Profile'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['password'],
            properties: [
                new OA\Property(property: 'password', type: 'string', format: 'password', description: 'Current account password for confirmation'),
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Account deleted',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Account deleted'),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Incorrect password')]
    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate(['password' => 'required|current_password:sanctum']);

        $user = $request->user();
        $user->tokens()->delete();
        $user->delete(); // Soft delete if using SoftDeletes trait, else add it

        return response()->json(['message' => 'Account deleted'], Response::HTTP_OK);
    }
}
