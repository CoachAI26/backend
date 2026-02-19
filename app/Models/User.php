<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'is_guest',
        'name', // display name
        'bio',
        'profile_picture', // URL to uploaded image
        'speaking_goals',  // json array e.g. ["build_confidence", "improve_clarity"]
        'notification_preferences', // json e.g. {"daily_reminder": true, "weekly_report": false}
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_guest' => 'boolean',
            'speaking_goals' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function practiceSessions()
    {
        return $this->hasMany(PracticeSession::class);
    }

    /**
     * Whether this user is a guest (free-tier, no account).
     */
    public function isGuest(): bool
    {
        return (bool) $this->is_guest;
    }
}
