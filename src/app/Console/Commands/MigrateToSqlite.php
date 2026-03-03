<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateToSqlite extends Command
{
    protected $signature = 'db:migrate-to-sqlite';
    protected $description = 'Migrates all data from MySQL to SQLite';

    /**
     * Tables to migrate, in dependency order (parents before children).
     * Temporary tables (cache, sessions, jobs) are intentionally excluded.
     */
    private array $tables = [
        'users',
        'settings',
        'creatures',
        'characters',
        'character_online_times',
        'character_creature_progress',
        'execution_crawlers',
    ];

    public function handle(): int
    {
        $this->info('Starting migration from MySQL to SQLite...');
        $this->newLine();

        DB::connection('sqlite')->statement('PRAGMA foreign_keys = OFF');

        foreach ($this->tables as $table) {
            $this->migrateTable($table);
        }

        DB::connection('sqlite')->statement('PRAGMA foreign_keys = ON');

        $this->newLine();
        $this->info('Migration completed successfully!');

        return self::SUCCESS;
    }

    private function migrateTable(string $table): void
    {
        $count = DB::connection('mysql')->table($table)->count();
        $this->line("  <fg=cyan>{$table}</> ({$count} records)");

        DB::connection('sqlite')->table($table)->delete();

        if ($count === 0) {
            $this->line("  <fg=yellow>Skipped (empty)</>");
            $this->newLine();
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        DB::connection('mysql')->table($table)->orderBy('id')->chunk(500, function ($rows) use ($table, $bar) {
            $data = $rows->map(fn($row) => (array) $row)->toArray();

            // SQLite has a ~999 variable limit per statement.
            // Batch size of 50 is safe for all tables (max 14 cols × 50 = 700).
            foreach (array_chunk($data, 50) as $batch) {
                DB::connection('sqlite')->table($table)->insert($batch);
            }

            $bar->advance(count($data));
        });

        $bar->finish();
        $this->newLine(2);
    }
}
