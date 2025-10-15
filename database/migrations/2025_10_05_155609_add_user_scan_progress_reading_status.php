<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ReadingStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_scan_progress', function (Blueprint $table) {
            $table->string('reading_status')->default(ReadingStatus::ONGOING);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_scan_progress', function (Blueprint $table) {
            $table->dropColumn('reading_status');
        });
    }
};
