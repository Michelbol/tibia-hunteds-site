<?php

namespace App\Http\Controllers;

use App\Setting\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Character\CharacterService;
use App\CharacterOnlineTime\CharacterOnlineTimeService;

class HomeController extends Controller {

    public function __construct(
        private readonly CharacterService $characterService,
        private readonly CharacterOnlineTimeService $characterOnlineTimeService,
        private readonly SettingService $settingService,
    ){}

    public function index(Request $request): View {
        $guildName = $this->settingService->getGuildName();
        $search = $request->get('guild_name');
        return view('observer', compact('guildName', 'search'));
    }

    public function getOnlineCharacters(Request $request): JsonResponse {
        $guildName = $request->get('guild_name');
        return response()->json(['onlineCharacters' => $this->characterService->retrieveOnlinePlayers($guildName)]);
    }

    public function setCharacterType(string $characterName, string $type): JsonResponse {
        $this->characterService->updateCharacterType($characterName, $type);
        return response()->json();
    }

    public function setCharacterAsAttacker(string $characterName, string $isAttacker): JsonResponse {
        $this->characterService->updateCharacterIsAttacker($characterName, $isAttacker === 'true');
        return response()->json();
    }

    public function updateCharacterPosition(Request $request, string $characterName): JsonResponse {
        $position = $request->get('position');
        if (is_null($position)) {
            return response()->json();
        }
        $this->characterService->updateCharacterPosition($characterName, $position);
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

    public function settings(): View {
        $setting = $this->settingService->getGuildName();
        $guildName = $setting?->value;
        return view('settings', compact('guildName'));
    }
    public function saveSettings(): View {
        $requestGuildName = request()->get('guild_name');
        $setting = $this->settingService->saveGuildName($requestGuildName);
        $guildName = $setting->value;
        return view('settings', compact('guildName'));
    }

    public function refil(): View {
        return view('refil');
    }
}
