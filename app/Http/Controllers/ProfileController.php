<?php

namespace App\Http\Controllers;

use App\Services\CodeBarkadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /** Show the member's profile with their current choices. */
    public function edit(Request $request, CodeBarkadaService $codeBarkada): View
    {
        return view('profile', [
            'member' => $request->user(),
            'tracks' => $codeBarkada->tracks(),
            'colors' => $codeBarkada->botColors(),
        ]);
    }

    /** Save a new codename, track or bot color (same rules as the welcome page). */
    public function update(Request $request, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate($codeBarkada->profileRules($user));

        $user->update([
            'codename' => $data['codename'],
            'track' => $data['track'],
            'avatar' => $codeBarkada->botAvatar((int) $data['color']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'Profile saved.');
    }
}
