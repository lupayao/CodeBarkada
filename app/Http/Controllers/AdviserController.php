<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CodeBarkadaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdviserController extends Controller
{
    /** The adviser dashboard: a summary of the whole club. */
    public function index(Request $request, CodeBarkadaService $codeBarkada): View
    {
        // One query for all members; the service does the counting.
        $members = User::where('role', User::ROLE_MEMBER)->get();

        return view('adviser.home', [
            'adviser' => $request->user(),
            'stats' => $codeBarkada->dashboard($members),
        ]);
    }
}
