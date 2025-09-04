<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('execution_crawlers', function (Blueprint $table) {
            $table->id();
            $table->string('guild_name');
            $table->string('url');
            $table->integer('qtd_characters');
            $table->integer('qtd_character_online');
            $table->integer('qtd_character_offline');
            $table->integer('execution_time');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('execution_crawler');
    }
};
