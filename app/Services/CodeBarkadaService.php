<?php

namespace App\Services;

use Illuminate\Support\Str;

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
}
