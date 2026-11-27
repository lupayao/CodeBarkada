<?php

namespace App\Http\Middleware;

use App\Services\CodeBarkadaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A guard at the door: only lets in the right kind of user.
 * Used in routes like ->middleware('role:adviser').
 * Anyone else is sent back to their own home page.
 */
class EnsureRole
{
    public function __construct(private CodeBarkadaService $codeBarkada) {}

    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($user->role !== $role) {
            return redirect()->route($this->codeBarkada->homeRouteFor($user));
        }

        return $next($request);
    }
}
