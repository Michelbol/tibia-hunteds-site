<?php

namespace App\Console\Commands;

use App\Scrapers\GuildPage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class WorldScraper extends Command {

    protected $signature = 'world-scraper';

    protected $description = 'Guild Page Scraper';

    public function handle(): void {
        try {
            $timestamp = Carbon::now()->timestamp;
            $html = Browsershot::url('https://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=Outlaw%20Warlords&timestamp='.$timestamp)
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->bodyHtml();

            $guildPage = app(GuildPage::class);
            $guildPage->scrap($html);
            $this->info('Guild Page Scraped Summary');
            $this->info('Total Characters: '. $guildPage->characters->count());
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
