<?php

use App\Console\Commands\ClearCharactersCache;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ClearCharactersCache::class)->everyMinute();
