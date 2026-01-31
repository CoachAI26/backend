<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartPracticeSessionRequest;
use App\Http\Resources\PracticeSessionResource;
use App\Models\PracticeSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PracticeSessionController extends Controller
{
    public function start(StartPracticeSessionRequest $request): JsonResponse
    {
        $session = PracticeSession::create([
            'user_id'      => $request->user()->id,
            'challenge_id' => $request->challenge_id,
            'name'         => $request->name, // optional
        ]);

        return response()->json(new PracticeSessionResource($session), Response::HTTP_CREATED);
    }

    public function show(PracticeSession $session): PracticeSessionResource
    {
        $session->load(['challenge.category', 'challenge.level', 'result']);

        return new PracticeSessionResource($session);
    }

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $sessions = $request->user()
            ->practiceSessions()
            ->with(['challenge', 'result'])
            ->latest()
            ->get();

        return PracticeSessionResource::collection($sessions);
    }
}
