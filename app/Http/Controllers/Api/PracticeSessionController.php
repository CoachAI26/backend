<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartPracticeSessionRequest;
use App\Http\Resources\PracticeSessionResource;
use App\Models\PracticeSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class PracticeSessionController extends Controller
{
    #[OA\Post(
        path: '/practice-sessions',
        summary: 'Start a new practice session',
        description: 'Creates a new practice session for the authenticated user, linked to a specific challenge.',
        security: [['sanctum' => []]],
        tags: ['Practice Sessions'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['challenge_id'],
            properties: [
                new OA\Property(property: 'challenge_id', type: 'integer', example: 1, description: 'ID of the challenge to practice'),
                new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'My morning practice', description: 'Optional custom session name. Auto-generated if omitted.'),
            ],
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Session created',
        content: new OA\JsonContent(ref: '#/components/schemas/PracticeSession'),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error (invalid challenge_id)')]
    public function start(StartPracticeSessionRequest $request): JsonResponse
    {
        $session = PracticeSession::create([
            'user_id'      => $request->user()->id,
            'challenge_id' => $request->challenge_id,
            'name'         => $request->name, // optional
        ]);

        return response()->json(new PracticeSessionResource($session), Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/practice-sessions/{id}',
        summary: 'Get a practice session',
        description: 'Returns a specific practice session with its challenge and result.',
        security: [['sanctum' => []]],
        tags: ['Practice Sessions'],
    )]
    #[OA\Parameter(name: 'id', in: 'path', required: true, description: 'Practice session ID', schema: new OA\Schema(type: 'integer'), example: 1)]
    #[OA\Response(
        response: 200,
        description: 'Session details',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/PracticeSession'),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 403, description: 'Session does not belong to the authenticated user')]
    #[OA\Response(response: 404, description: 'Session not found')]
    public function show(Request $request, PracticeSession $session): PracticeSessionResource
    {
        abort_unless($session->user_id === $request->user()->id, 403);

        $session->load(['challenge.category', 'challenge.level', 'result']);

        return new PracticeSessionResource($session);
    }

    #[OA\Get(
        path: '/practice-sessions',
        summary: 'List practice sessions',
        description: 'Returns all practice sessions for the authenticated user, ordered by most recent first.',
        security: [['sanctum' => []]],
        tags: ['Practice Sessions'],
    )]
    #[OA\Response(
        response: 200,
        description: 'List of sessions',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PracticeSession')),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
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
