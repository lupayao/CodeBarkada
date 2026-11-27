<?php

namespace App\Http\Middleware;

use App\Services\CodeBarkadaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A guard at the door: a member who hasn't picked a codename, track
 * and avatar yet is sent to the welcome page first.
 */
class EnsureProfileDone
{
    public function __construct(private CodeBarkadaService $codeBarkada) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $this->codeBarkada->needsProfile($request->user())) {
            return redirect()->route('welcome.edit');
        }

        return $next($request);
    }
}
