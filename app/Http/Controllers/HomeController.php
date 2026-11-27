<?php

namespace App\Http\Controllers;

use App\Services\CodeBarkadaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the member home page.
     * Laravel hands us the service automatically (we just ask for it in the brackets).
     */
    public function index(Request $request, CodeBarkadaService $codeBarkada): View
    {
        return view('home', [
            'member' => $request->user(),
            'tagline' => $codeBarkada->tagline(),
        ]);
    }
}
