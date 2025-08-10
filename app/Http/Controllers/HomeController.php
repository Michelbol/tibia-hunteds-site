<?php

namespace App\Http\Controllers;

use App\Character\CharacterService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HomeController extends Controller {

    public function __construct(
        private readonly CharacterService $characterService,
    ){}

    public function index(): View {
        return view('observer');
    }

    public function getOnlineCharacters(): JsonResponse {
        return response()->json(['onlineCharacters' => $this->characterService->retrieveOnlinePlayers()]);
    }

    public function setCharacterType(string $characterName, string $type): JsonResponse {
        $this->characterService->updateCharacterType($characterName, $type);
        return response()->json();
    }

    public function getCharactersOnlineGantGraphics(): View {
        return view('online-graphics-gant');
    }
}
