<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->timestamp('position_time')->nullable();
        });
    }

    public function down(): void {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('position_time');
        });
    }
};
