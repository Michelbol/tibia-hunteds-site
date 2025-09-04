<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('execution_crawlers', function (Blueprint $table) {
            $table->float('scraping_time')->default(0);
            $table->float('request_time')->default(0);
        });
    }

    public function down(): void {
        Schema::table('execution_crawlers', function (Blueprint $table) {
            $table->dropColumn('scraping_time');
            $table->dropColumn('request_time');
        });
    }
};
