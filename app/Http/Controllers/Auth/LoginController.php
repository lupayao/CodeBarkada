<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CodeBarkadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Show the sign-in form. */
    public function show(): View
    {
        return view('auth.login', [
            'emailDomain' => config('codebarkada.email_domain'),
        ]);
    }

    /** Check the email and password, then sign in. */
    public function store(Request $request, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! $codeBarkada->isSchoolEmail($data['email'])) {
            return back()->withErrors(['email' => 'Use your school email ('.config('codebarkada.email_domain').').'])->onlyInput('email');
        }

        $credentials = ['email' => Str::lower(trim($data['email'])), 'password' => $data['password']];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Wrong email or password.'])->onlyInput('email');
        }

        // New session id after signing in (protects against session hijacking).
        $request->session()->regenerate();

        return redirect()->route($codeBarkada->homeRouteFor($request->user()));
    }

    /** Sign out and go back to the sign-in page. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
