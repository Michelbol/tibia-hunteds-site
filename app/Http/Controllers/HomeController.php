<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Character\CharacterService;
use App\CharacterOnlineTime\CharacterOnlineTimeService;

class HomeController extends Controller {

    public function __construct(
        private readonly CharacterService $characterService,
        private readonly CharacterOnlineTimeService $characterOnlineTimeService,
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
        $info = $this->characterOnlineTimeService->retrieveOnlineTimeByOnlineAt(now());
        return view('online-graphics-gant', compact('info'));
    }
}
