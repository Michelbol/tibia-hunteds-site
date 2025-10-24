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
                ->setChromePath(env('PUPPETEER_EXECUTABLE_PATH', '/usr/bin/google-chrome'))
                ->noSandbox() // adiciona --no-sandbox
                ->setOption('args', [
                    // flags essenciais para rodar headless em container
                    '--disable-gpu',
                    '--disable-dev-shm-usage',
                    '--disable-software-rasterizer',
                    '--disable-extensions',
                    '--disable-background-networking',
                    '--disable-background-timer-throttling',
                    '--disable-client-side-phishing-detection',
                    '--disable-default-apps',
                    '--disable-setuid-sandbox',
                    '--no-zygote',
                    '--single-process',
                    '--no-first-run',
                    '--no-default-browser-check',
                    '--disable-blink-features=AutomationControlled',
                    // opcional: usar user-data-dir temporário se quiser cookies persistidos
                    '--user-data-dir=/tmp/chrome-user-data',
                ])
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.5993.70 Safari/537.36')
                ->setExtraHttpHeaders([
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'sec-ch-ua' => '"Chromium";v="118", "Google Chrome";v="118", "Not A;Brand";v="24"',
                    'sec-ch-ua-platform' => '"Windows"',
                ])
                ->waitUntilNetworkIdle()
                ->setDelay(2000) // espera extra (ms) pra passar verificações JS
                // injeta pequenos scripts pra mascarar navigator.webdriver e outros sinais
                ->evaluateJavascript(<<<'JS'
        delete navigator.__proto__.webdriver;
        Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
        // plugins & mimeTypes (básico)
        Object.defineProperty(navigator, 'plugins', { get: () => [1,2,3,4,5] });
        Object.defineProperty(navigator, 'languages', { get: () => ['en-US','en'] });
        true;
    JS
                )
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
