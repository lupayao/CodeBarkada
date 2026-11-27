<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CodeBarkadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /** Show the change-password form. */
    public function edit(Request $request): View
    {
        return view('auth.change-password', [
            'mustChange' => $request->user()->must_change_password,
        ]);
    }

    /** Save the new password. */
    public function update(Request $request, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => $codeBarkada->newPasswordRules(),
        ]);

        $user = $request->user();

        // Did this device tick "Keep me signed in"? (It has a remember-me cookie.)
        $remember = $request->hasCookie(Auth::getRecallerName());

        // Save the new password. Other devices still carry the old password's
        // fingerprint, so the "auth.session" guard signs them out on their next click.
        $user->update([
            'password' => $data['password'],
            'must_change_password' => false,
        ]);

        // Keep THIS device signed in with the new password, and give it a fresh session id.
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->route($codeBarkada->homeRouteFor($user))
            ->with('status', 'Password changed.');
    }
}
