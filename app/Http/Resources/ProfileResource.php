<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'profile_picture' => $this->profile_picture,
            'speaking_goals' => $this->speaking_goals ?? [],
            'notification_preferences' => $this->notification_preferences ?? [],
            'statistics' => $this->when(isset($this->statistics), $this->statistics),
            'achievements' => $this->when(isset($this->achievements), $this->achievements),
        ];
    }
}
