<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('online_characters', function (Blueprint $table) {
            $table->string('type')->default('main');
        });
    }

    public function down(): void {
        Schema::table('online_characters', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
