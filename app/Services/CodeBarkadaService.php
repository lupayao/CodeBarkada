<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * All CodeBarkada school rules live here: typing speed scores,
 * quiz scoring, skill profiles, rankings.
 * Controllers ask this class; views only show the answers.
 */
class CodeBarkadaService
{
    /**
     * The club's one-line description, shown on the home page.
     */
    public function tagline(): string
    {
        return 'Practice typing, sharpen your coding logic, and get ready for CS competitions.';
    }

    /**
     * School rule: every account must use a school email (@crc.pshs.edu.ph).
     * Capital letters don't matter, so "Ana@CRC.pshs.edu.ph" is fine too.
     */
    public function isSchoolEmail(string $email): bool
    {
        $email = Str::lower(trim($email));
        $domain = config('codebarkada.email_domain');

        // Something must come before the @, e.g. "ana@crc.pshs.edu.ph".
        return Str::endsWith($email, $domain) && Str::length($email) > Str::length($domain);
    }

    /**
     * Where each kind of user lands after signing in (a route name).
     */
    public function homeRouteFor(User $user): string
    {
        return $user->isAdviser() ? 'adviser.home' : 'home';
    }

    /**
     * Rules for a new password: at least 8 characters, typed twice
     * (the second box is "password_confirmation"), and not the same as the old one.
     */
    public function newPasswordRules(): array
    {
        return ['required', 'confirmed', 'different:current_password', Password::min(8)];
    }
}
