<?php

namespace App\Console\Commands;

use App\Models\ExecutionCrawler;
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
            $globalExecutionBegin = microtime(true);
            $guild = $this->argument('guild');
            if ($guild === self::GUILDS_NAME[0]['id']) {
                $searchGuild = self::GUILDS_NAME[0]['name'];
            } else {
                $searchGuild = self::GUILDS_NAME[1]['name'];
            }
            $timestamp = Carbon::now()->timestamp;
            $url = "https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=$searchGuild&timestamp=$timestamp" . random_int(100, 200);
            $requestTimeBegin = microtime(true);
            $html = Browsershot::url($url)
                ->noSandbox()
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115 Safari/537.36')
                ->setChromePath('/usr/bin/google-chrome')
                ->windowSize(1366, 768)
                ->timeout(60000)
                ->setOption('headless', false)
                ->waitUntilNetworkIdle()
                ->bodyHtml();
            $requestTimeEnd = microtime(true);

            $scrapingTimeBegin = microtime(true);
            $guildPage = app(GuildPage::class);
            $guildPage->scrap($html, $searchGuild);
            $scrapingTimeEnd = microtime(true);

            $globalExecutionEnd = microtime(true);
            $executionTime = $globalExecutionEnd - $globalExecutionBegin;
            $scrapingTime = $scrapingTimeEnd - $scrapingTimeBegin;
            $requestTime = $requestTimeEnd - $requestTimeBegin;
            $this->createExecutionCrawler($searchGuild, $url, $guildPage, $executionTime, $scrapingTime, $requestTime);


            $this->info('Guild Page Scraped Summary');
            $this->info('Total Characters: '. $guildPage->characters->count());
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }

    private function createExecutionCrawler(string $searchGuild, string $url, GuildPage $guildPage, float $executionTime, float $scrapingTime, float $requestTime): void {
        $executionCrawler = new ExecutionCrawler();
        $executionCrawler->guild_name = $searchGuild;
        $executionCrawler->url = $url;
        $executionCrawler->qtd_characters = $guildPage->characters->count();
        $executionCrawler->qtd_character_online = $guildPage->getOnlineCharacters();
        $executionCrawler->qtd_character_offline = $guildPage->getOfflineCharacters();
        $executionCrawler->execution_time = $executionTime;
        $executionCrawler->scraping_time = $scrapingTime;
        $executionCrawler->request_time = $requestTime;
        $executionCrawler->save();
    }
}
