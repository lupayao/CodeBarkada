<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AdviserController extends Controller
{
    /** The adviser's home page (a placeholder for now). */
    public function index(Request $request): View
    {
        return view('adviser.home', [
            'adviser' => $request->user(),
        ]);
    }
}
