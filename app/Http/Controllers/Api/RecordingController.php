<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecordingRequest;
use App\Http\Resources\PracticeSessionResource;
use App\Models\PracticeSession;
use App\Services\SpeechAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RecordingController extends Controller
{
    public function __construct(
        protected SpeechAnalysisService $speechAnalysis
    ) {}

    public function store(StoreRecordingRequest $request): JsonResponse
    {
        $session = PracticeSession::findOrFail($request->practice_session_id);

        // Security: ensure it belongs to authenticated user
        abort_unless($session->user_id === $request->user()->id, 403);

        $challenge = $session->challenge()->with(['category', 'level'])->firstOrFail();
        $audioPath = $request->file('audio')->path();

        // ── AI Speech Analysis ──────────────────────────────────────
        try {
            $analysis = $this->speechAnalysis->analyze(
                audioPath: $audioPath,
                level:     $challenge->level->name ?? '',
                category:  $challenge->category->name ?? '',
                title:     $challenge->title,
            );

            $transcription = $analysis['text'] ?? '';
            $feedback      = $this->speechAnalysis->buildFeedback($analysis);
            $score         = $analysis['confidence_score'] ?? 0;
            $metadata      = $this->speechAnalysis->buildMetadata($analysis);
            $status        = 'processed';
        } catch (\Throwable $e) {
            Log::error('Speech analysis failed', [
                'session_id' => $session->id,
                'error'      => $e->getMessage(),
            ]);

            $transcription = '';
            $feedback      = 'Analysis could not be completed. Please try again.';
            $score         = 0;
            $metadata      = ['error' => $e->getMessage()];
            $status        = 'failed';
        }
        // ────────────────────────────────────────────────────────────

        // Save result
        $result = $session->result()->create([
            'transcription' => $transcription,
            'feedback'      => $feedback,
            'score'         => $score,
            'metadata'      => $metadata,
        ]);

        // Update session status
        $session->update([
            'status'       => $status,
            'completed_at' => now(),
        ]);

        return response()->json([
            'session' => new PracticeSessionResource($session->fresh()->load('result')),
            'result'  => $result,
        ], Response::HTTP_OK);
    }
}
