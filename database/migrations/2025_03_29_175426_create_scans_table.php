<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->timestamp('create_date');
            $table->timestamp('last_update')->nullable();
            $table->string('title')->unique();
            $table->string('summary')->nullable();
            $table->float('current_chapter');
            $table->string('cover_image')->nullable();
            $table->string('link_to_scan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
