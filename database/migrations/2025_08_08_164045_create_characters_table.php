<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('vocation');
            $table->integer('level');
            $table->date('joining_date');
            $table->string('type')->default('main');
            $table->boolean('is_online')->default(false);
            $table->timestamp('online_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('characters');
    }
};
