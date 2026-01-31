<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PracticeSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'challenge'     => new ChallengeResource($this->whenLoaded('challenge')),
            'started_at'    => $this->started_at,
            'completed_at'  => $this->completed_at,
            'status'        => $this->status,
            'result'        => $this->whenLoaded('result', fn () => [
                'transcription' => $this->result->transcription,
                'feedback'      => $this->result->feedback,
                'score'         => $this->result->score,
                'metadata'      => $this->result->metadata,
            ]),
        ];
    }
}
