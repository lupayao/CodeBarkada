<?php

namespace App\Http\Controllers;

use App\Services\CodeBarkadaService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the home page.
     * Laravel hands us the service automatically (we just ask for it in the brackets).
     */
    public function index(CodeBarkadaService $codeBarkada): View
    {
        return view('home', [
            'tagline' => $codeBarkada->tagline(),
        ]);
    }
}
