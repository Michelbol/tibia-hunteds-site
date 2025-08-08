<?php

namespace App\Http\Controllers;

use App\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HomeController extends Controller {

    public function __construct(){}

    public function index(): View {
        return view('observer');
    }

    public function getOnlineCharacters(): JsonResponse {
        return response()->json([
            'onlineCharacters' => Character::where('is_online', true)->get()
        ]);
    }
}
