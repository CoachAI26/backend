<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'suggested_time_minutes' => $this->suggested_time_minutes,
            'hints_available' => $this->hints_available,
            'tips' => $this->tips,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'level' => new LevelResource($this->whenLoaded('level')),
        ];
    }
}
