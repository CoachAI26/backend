<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecordingRequest;
use App\Models\Challenge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\PracticeSession;
use App\Http\Resources\PracticeSessionResource;

class RecordingController extends Controller
{
    public function store(StoreRecordingRequest $request): JsonResponse
    {
        $session = PracticeSession::findOrFail($request->practice_session_id); // new field!
        // Security: ensure it belongs to authenticated user
        abort_unless($session->user_id === $request->user()->id, 403);

        $challenge = $session->challenge()->with(['category', 'level'])->firstOrFail();
        $audioPath = $request->file('audio')->path();

        // AI PART 



        // Save result
        $result = $session->result()->create([
            'transcription' => "",
            'feedback'      => "",
            'score'         => "",
            'metadata'      => ['segments' => ""], // or more parsed data
        ]);

        // Update session status
        $session->update([
            'status'       => 'processed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'session'   => new PracticeSessionResource($session->fresh()->load('result')),
            'result'    => $result,
        ], Response::HTTP_OK);
    }
}
