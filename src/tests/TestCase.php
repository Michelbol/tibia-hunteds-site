<?php

namespace Tests;

use App\Scrapers\GuildPage;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase {
    public function tearDown(): void {
        $this->deleteAllRecordsFromDatabase();
        parent::tearDown();
        GuildPage::reset();
    }
    private function deleteAllRecordsFromDatabase(): void {
        DB::table('cache')->truncate();
        DB::table('cache_locks')->truncate();
        DB::table('failed_jobs')->truncate();
        DB::table('job_batches')->truncate();
        DB::table('jobs')->truncate();
        DB::table('sessions')->truncate();
        DB::table('users')->truncate();
        DB::table('character_online_times')->truncate();
        DB::table('characters')->truncate();
        DB::table('execution_crawlers')->truncate();
    }
}
