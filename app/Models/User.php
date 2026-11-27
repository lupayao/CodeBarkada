<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password',
    'role', 'grade', 'section', 'codename', 'track', 'avatar',
    'must_change_password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // The two kinds of users in the club.
    public const ROLE_ADVISER = 'adviser';

    public const ROLE_MEMBER = 'member';

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
            'must_change_password' => 'boolean',
        ];
    }

    /** Is this the club adviser? */
    public function isAdviser(): bool
    {
        return $this->role === self::ROLE_ADVISER;
    }

    /** Is this a club member? */
    public function isMember(): bool
    {
        return $this->role === self::ROLE_MEMBER;
    }

    /** The name to show on pages: the codename if chosen, otherwise the real name. */
    public function displayName(): string
    {
        return $this->codename ?: $this->name;
    }
}
