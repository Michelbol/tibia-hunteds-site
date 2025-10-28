<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class GetGuildPageCommand extends Command {
    protected $signature = 'get-guild-page {guild?}';
    protected $description = 'Get guild page and save into database';

    private const GUILDS_NAME = [
        [
            'id' => 'time',
            'name' => 'quelibraland'
        ],
        [
            'id' => 'contra',
            'name' => 'Outlaw%20Warlords'
        ],
    ];

    public function handle(): void {
        $searchGuild = $this->resolveGuildToSearch();
        $requestTimeBegin = microtime(true);
        $timestamp = Carbon::now()->timestamp;
        $url = "https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=$searchGuild&timestamp=$timestamp".random_int(100, 200);
        $html = Browsershot::url($url)
            ->setChromePath(env('PUPPETEER_EXECUTABLE_PATH', '/usr/bin/google-chrome'))
            ->noSandbox()
            ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115 Safari/537.36')
            ->waitUntilNetworkIdle()
            ->bodyHtml();
        $requestTimeEnd = microtime(true);
        Storage::put(storage_path("guilds/$searchGuild/guilds-$timestamp.html"), $html);
    }

    private function resolveGuildToSearch(): string {
        $guild = $this->argument('guild');
        if ($guild === self::GUILDS_NAME[0]['id']) {
            return self::GUILDS_NAME[0]['name'];
        }
        return self::GUILDS_NAME[1]['name'];
    }
}
