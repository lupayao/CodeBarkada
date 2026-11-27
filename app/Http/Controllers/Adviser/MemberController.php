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

    /** The Manage page for one member. */
    public function show(User $member, CodeBarkadaService $codeBarkada): View
    {
        abort_unless($codeBarkada->isManageable($member), 404);

        return view('adviser.member', [
            'member' => $member,
            'status' => $codeBarkada->memberStatus($member),
            'tracks' => $codeBarkada->tracks(),
        ]);
    }

    /** Give the member a new default password. */
    public function resetPassword(Request $request, User $member, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        abort_unless($codeBarkada->isManageable($member), 404);

        $data = $request->validate([
            'default_password' => $codeBarkada->defaultPasswordRules(),
        ]);

        $codeBarkada->resetMemberPassword($member, $data['default_password']);

        return redirect()->route('adviser.members.index')
            ->with('status', "Password reset for {$member->name}. They must change it when they next sign in.");
    }

    /** Delete the member (only when the confirm box is ticked). */
    public function destroy(Request $request, User $member, CodeBarkadaService $codeBarkada): RedirectResponse
    {
        abort_unless($codeBarkada->isManageable($member), 404);

        $request->validate([
            'confirm' => ['accepted'],
        ], [
            'confirm.accepted' => 'Tick the box to confirm.',
        ]);

        $codeBarkada->deleteMember($member);

        return redirect()->route('adviser.members.index')
            ->with('status', "{$member->name} was deleted.");
    }
}
