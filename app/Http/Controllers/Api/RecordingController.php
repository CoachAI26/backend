<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SpeechAnalysisException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecordingRequest;
use App\Http\Resources\PracticeSessionResource;
use App\Models\PracticeSession;
use App\Services\SpeechAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class RecordingController extends Controller
{
    public function __construct(
        protected SpeechAnalysisService $speechAnalysis
    ) {}

    #[OA\Post(
        path: '/recordings',
        summary: 'Upload a recording for AI analysis',
        description: 'Uploads an audio recording for a practice session. The audio is sent to the AI speech analysis engine which returns transcription, fluency scores, filler word detection, pace analysis, and personalised recommendations.',
        security: [['sanctum' => []]],
        tags: ['Recordings'],
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['practice_session_id', 'audio'],
                properties: [
                    new OA\Property(property: 'practice_session_id', type: 'integer', example: 1, description: 'ID of the practice session this recording belongs to'),
                    new OA\Property(property: 'audio', type: 'string', format: 'binary', description: 'Audio file (MP3, WAV, M4A). Max 10 MB.'),
                ],
            ),
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Recording processed successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'session', ref: '#/components/schemas/PracticeSession'),
                new OA\Property(property: 'result', ref: '#/components/schemas/PracticeResult'),
            ],
        ),
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 403, description: 'Session does not belong to the authenticated user')]
    #[OA\Response(response: 404, description: 'Practice session not found')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function store(StoreRecordingRequest $request): JsonResponse
    {
        $session = PracticeSession::findOrFail($request->practice_session_id);

        // Security: ensure it belongs to authenticated user
        abort_unless($session->user_id === $request->user()->id, 403);

        $challenge = $session->challenge()->with(['category', 'level'])->firstOrFail();
        $audio     = $request->file('audio');
        $audioPath = $audio->path();

        // ── AI Speech Analysis ──────────────────────────────────────
        try {
            $analysis = $this->speechAnalysis->analyze(
                audioPath: $audioPath,
                level:     $challenge->level->name ?? '',
                category:  $challenge->category->name ?? '',
                title:     $challenge->title,
                filename:  $audio->getClientOriginalName(),
            );

            $transcription = $analysis['text'] ?? '';
            $feedback      = $this->speechAnalysis->buildFeedback($analysis);
            $score         = $analysis['confidence_score'] ?? 0;
            $improvedText  = $analysis['improved_text'] ?? null;
            $metadata      = $this->speechAnalysis->buildMetadata($analysis);
            $status        = 'processed';
        } catch (SpeechAnalysisException $e) {
            Log::error('Speech analysis failed', [
                'session_id'  => $session->id,
                'http_status' => $e->httpStatus,
                'error'       => $e->getMessage(),
            ]);

            $transcription = '';
            $feedback      = $e->httpStatus === 400
                ? 'Please speak in English. Other languages are not accepted.'
                : 'Analysis could not be completed. Please try again.';
            $score         = 0;
            $improvedText  = null;
            $metadata      = ['error' => $e->getMessage()];
            $status        = 'failed';
        } catch (\Throwable $e) {
            Log::error('Speech analysis failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);

            $transcription = '';
            $feedback      = 'Analysis could not be completed. Please try again.';
            $score         = 0;
            $improvedText  = null;
            $metadata      = ['error' => $e->getMessage()];
            $status        = 'failed';
        }
        // ────────────────────────────────────────────────────────────

        // Save result
        $result = $session->result()->create([
            'transcription' => $transcription,
            'feedback'      => $feedback,
            'improved_text' => $improvedText,
            'score'         => $score,
            'metadata'      => $metadata,
        ]);

        // Update session status
        $session->update([
            'status'       => $status,
            'completed_at' => now(),
        ]);

        Log::info('Speech analysis API request payload', [
            'improved_text' => $improvedText,
            'result' => $result,
        ]);

        return response()->json([
            'session' => new PracticeSessionResource($session->fresh()->load('result')),
            'result'  => $result,
            'improved_text' => $improvedText
        ], Response::HTTP_OK);
    }
}
