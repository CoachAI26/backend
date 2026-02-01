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

class ProfileController extends Controller
{
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

    public function history(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $sessions = $request->user()
            ->practiceSessions()
            ->with(['challenge.category', 'challenge.level', 'result'])
            ->latest()
            ->get();

        return PracticeSessionResource::collection($sessions);
    }

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

    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate(['password' => 'required|current_password:sanctum']);

        $user = $request->user();
        $user->tokens()->delete();
        $user->delete(); // Soft delete if using SoftDeletes trait, else add it

        return response()->json(['message' => 'Account deleted'], Response::HTTP_OK);
    }
}
