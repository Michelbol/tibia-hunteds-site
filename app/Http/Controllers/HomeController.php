<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $requestDate = now();
        $dateFromRequest = request()->get('date');
        if (!is_null($dateFromRequest) && Carbon::hasFormat($dateFromRequest, 'Y-m-d')){
            $requestDate = Carbon::createFromFormat('Y-m-d', request()->get('date'));
        }
        $info = $this->characterOnlineTimeService->retrieveOnlineTimeByOnlineAt($requestDate);
        $day = $requestDate->format('Y-m-d');
        return view('online-graphics-gant', compact('info', 'day'));
    }
}
