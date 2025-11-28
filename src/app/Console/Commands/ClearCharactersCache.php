<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearCharactersCache extends Command {

    protected $signature = 'clear-characters-cache';
    protected $description = 'Clear all characters cache';

    public function handle(): void {
        $this->info(now()->format('Y-m-d-H-i-s'));
        $filesToKeep = [
            'cache/online-characters/'.now()->addSeconds(6)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->addSeconds(5)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->addSeconds(4)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->addSeconds(3)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->addSeconds(2)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->addSecond()->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSecond()->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(2)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(3)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(4)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(5)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(6)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(7)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(8)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(9)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(10)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(11)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(12)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(13)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(14)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(15)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(16)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(17)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(18)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(19)->format('Y-m-d-H-i-s'),
            'cache/online-characters/'.now()->subSeconds(20)->format('Y-m-d-H-i-s'),
        ];


        $files = Storage::disk('local')->files('cache/online-characters');
        $this->info(json_encode($filesToKeep));
        $this->info('==================================================');
        $this->info(json_encode($files));
        foreach ($files as $file) {
            if (in_array($file, $filesToKeep)) {
               continue;
            }
            Storage::disk('local')->delete($file);
        }
    }
}
