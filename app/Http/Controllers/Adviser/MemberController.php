<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CodeBarkadaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    /** The member list and the "Add members" form. */
    public function index(CodeBarkadaService $codeBarkada): View
    {
        // Sorted by grade (as a number, so 7 comes before 10), then section, then name.
        // Members without a grade go last.
        $members = User::where('role', User::ROLE_MEMBER)
            ->orderByRaw('grade IS NULL, CAST(grade AS UNSIGNED)')
            ->orderBy('section')
            ->orderBy('name')
            ->get();

        return view('adviser.members', [
            'members' => $members,
            'statuses' => $members->mapWithKeys(fn (User $m) => [$m->id => $codeBarkada->memberStatus($m)]),
            'tracks' => $codeBarkada->tracks(),
            'emailDomain' => config('codebarkada.email_domain'),
        ]);
    }

    /** Add the pasted members, then come back with a summary. */
    public function store(Request $request, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        $data = $request->validate([
            'members' => ['required', 'string', 'max:20000'],
            'default_password' => $codeBarkada->defaultPasswordRules(),
        ]);

        $summary = $codeBarkada->addMembers($data['members'], $data['default_password']);

        return redirect()->route('adviser.members.index')->with('status', $summary);
    }
}
