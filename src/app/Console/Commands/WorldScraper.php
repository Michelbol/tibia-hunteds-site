<?php

namespace App\Console\Commands;

use App\Models\ExecutionCrawler;
use App\Scrapers\GuildPage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class WorldScraper extends Command {

    protected $signature = 'world-scraper {guild?} {debuggerMode?}';
    protected $description = 'Guild Page Scraper';
    private ?string $guild;
    private float $requestTime;
    private float $scrapTime;

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
        try {
            $globalExecutionBegin = microtime(true);

            $searchGuild = $this->resolveGuildToSearch();
            $timestamp = Carbon::now()->timestamp;
            $url = "https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=$searchGuild&timestamp=$timestamp" . random_int(100, 200);
            $html = $this->dispatchRequest($url);
            $guildPage = $this->scrapPage($html, $searchGuild);

            $globalExecutionEnd = microtime(true);
            $executionTime = $globalExecutionEnd - $globalExecutionBegin;
            $this->createExecutionCrawler($searchGuild, $url, $guildPage, $executionTime);

            $this->info('Guild Page Scraped Summary');
            $this->info('Total Characters: '. $guildPage->getOnlineCharacters()+$guildPage->getOfflineCharacters());
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }

    private function createExecutionCrawler(string $searchGuild, string $url, GuildPage $guildPage, float $executionTime): void {
        $executionCrawler = new ExecutionCrawler();
        $executionCrawler->guild_name = $searchGuild;
        $executionCrawler->url = $url;
        $executionCrawler->qtd_characters = $guildPage->getOnlineCharacters()+$guildPage->getOfflineCharacters();
        $executionCrawler->qtd_character_online = $guildPage->getOnlineCharacters();
        $executionCrawler->qtd_character_offline = $guildPage->getOfflineCharacters();
        $executionCrawler->execution_time = $executionTime;
        $executionCrawler->scraping_time = $this->scrapTime;
        $executionCrawler->request_time = $this->requestTime;
        $executionCrawler->save();
    }

    private function dispatchRequest(string $url): string {
        $requestTimeBegin = microtime(true);
        $html = Browsershot::url($url)
            ->setChromePath(env('PUPPETEER_EXECUTABLE_PATH', '/usr/bin/google-chrome'))
            ->noSandbox()
            ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115 Safari/537.36')
            ->waitUntilNetworkIdle()
            ->bodyHtml();
        $requestTimeEnd = microtime(true);
        $this->requestTime = $requestTimeEnd - $requestTimeBegin;
        return $html;
    }

    private function scrapPage(string $html, string $searchGuild): GuildPage {
        $scrapingTimeBegin = microtime(true);
        $guildPage = app(GuildPage::class);
        $guildPage->scrap($html, $searchGuild);
        $scrapingTimeEnd = microtime(true);
        $this->scrapTime = $scrapingTimeEnd - $scrapingTimeBegin;
        return $guildPage;
    }

    private function resolveGuildToSearch(): string {
        $this->guild = $this->argument('guild');
        if ($this->guild === self::GUILDS_NAME[0]['id']) {
            return self::GUILDS_NAME[0]['name'];
        }
        return self::GUILDS_NAME[1]['name'];
    }
}
