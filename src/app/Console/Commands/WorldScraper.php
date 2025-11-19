<?php

namespace App\Console\Commands;

use App\Models\ExecutionCrawler;
use App\Scrapers\GuildPage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use \Spatie\Browsershot\Browsershot;

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

            $this->info('Guild Page Scraped Summary: '.Carbon::now()->toDateTimeString());
            $this->info('Total Characters: '. $guildPage->getOnlineCharacters()+$guildPage->getOfflineCharacters());
        } catch (\Exception $e) {
            $this->info('Error to execute: '.Carbon::now()->toDateTimeString());
            Log::info($e->getMessage());
        } finally {
            $qtdTotal = 0;
            $qtdOnline = 0;
            $qtdOffline = 0;
            if (is_null($searchGuild)) {
                $searchGuild = 'Search didnt exists';
            }
            if (is_null($url)) {
                $searchGuild = 'Url didnt exists';
            }
            if (isset($guildPage)) {
                $qtdTotal = $guildPage->getOnlineCharacters()+$guildPage->getOfflineCharacters();
                $qtdOnline = $guildPage->getOnlineCharacters();
                $qtdOffline = $guildPage->getOfflineCharacters();
            }
            if (!isset($globalExecutionBegin)) {
                $globalExecutionBegin = microtime(true);
            }
            if (!isset($globalExecutionEnd)) {
                $globalExecutionEnd = microtime(true);
            }
            if (!isset($executionTime)) {
                $executionTime = $globalExecutionEnd - $globalExecutionBegin;
            }
            if (!isset($this->scrapTime)) {
                $this->scrapTime = 0;
            }
            if (!isset($this->requestTime)) {
                $this->requestTime = 0;
            }
            $this->createExecutionCrawler($searchGuild, $url, $qtdTotal, $qtdOnline, $qtdOffline, $executionTime, $this->scrapTime, $this->requestTime);
        }
    }

    private function createExecutionCrawler(
        string $searchGuild,
        string $url,
        int $qtdTtalCharacters,
        int $qtdOnline,
        int $qtdOffline,
        float $executionTime,
        float $scrapTime,
        float $requestTime
    ): void {
        $executionCrawler = new ExecutionCrawler();
        $executionCrawler->guild_name = $searchGuild;
        $executionCrawler->url = $url;
        $executionCrawler->qtd_characters = $qtdTtalCharacters;
        $executionCrawler->qtd_character_online = $qtdOnline;
        $executionCrawler->qtd_character_offline = $qtdOffline;
        $executionCrawler->execution_time = $executionTime;
        $executionCrawler->scraping_time = $scrapTime;
        $executionCrawler->request_time = $requestTime;
        $executionCrawler->save();
    }

    private function dispatchRequest(string $url): string {
        $requestTimeBegin = microtime(true);
        $client = new Client([
            'base_uri' => 'https://www.tibia.com',
            'timeout'  => 15,
            'allow_redirects' => true,
        ]);

        $response = $client->get('/community/', [
            'query' => [
                'subtopic'  => 'guilds',
                'page'      => 'view',
                'GuildName' => 'Outlaw Warlords',
            ],
            'headers' => [
                'Authority' => 'www.tibia.com',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9,pt-BR;q=0.8,pt;q=0.7',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'none',
                'Sec-Fetch-User' => '?1',
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            ],
        ]);

        $html = $response->getBody()->getContents();
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
