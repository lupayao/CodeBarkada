<?php

namespace App\Http\Controllers;

use App\Services\CodeBarkadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /** Show the first-sign-in form: codename, track and avatar color. */
    public function edit(Request $request, CodeBarkadaService $codeBarkada): View
    {
        return view('welcome', [
            'member' => $request->user(),
            'tracks' => $codeBarkada->tracks(),
            'colors' => $codeBarkada->botColors(),
        ]);
    }

    /** Save the member's choices, then open the hub. */
    public function update(Request $request, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate($codeBarkada->profileRules($user));

        $user->update([
            'codename' => $data['codename'],
            'track' => $data['track'],
            'avatar' => $codeBarkada->botAvatar((int) $data['color']),
        ]);

        return redirect()->route('home')
            ->with('status', "Welcome to the barkada, {$user->codename}!");
    }
}
