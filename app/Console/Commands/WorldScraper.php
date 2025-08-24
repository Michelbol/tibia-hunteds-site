<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Scrapers\GuildPage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class WorldScraper extends Command {

    protected $signature = 'world-scraper {guild?}';

    protected $description = 'Guild Page Scraper';

    private const GUILDS_NAME = [
        [
            'id' => 'contra',
            'name' => 'Outlaw%20Warlords'
        ],
        [
            'id' => 'time',
            'name' => 'quelibraland'
        ]
    ];

    public function handle(): void {
        try {
            $guild = $this->argument('guild');
            if ($guild === self::GUILDS_NAME[0]['id']) {
                $searchGuild = self::GUILDS_NAME[0]['name'];
            } else {
                $searchGuild = self::GUILDS_NAME[1]['name'];
            }
            $timestamp = Carbon::now()->timestamp;
            $html = Browsershot::url("https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=$searchGuild&timestamp=$timestamp".random_int(100,200))
                ->noSandbox()
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115 Safari/537.36')
                ->waitUntilNetworkIdle()
                ->bodyHtml();

            $guildPage = app(GuildPage::class);
            $guildPage->scrap($html, $searchGuild);
            $this->info('Guild Page Scraped Summary');
            $this->info('Total Characters: '. $guildPage->characters->count());
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
