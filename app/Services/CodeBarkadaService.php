<?php

namespace App\Services;

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
}
