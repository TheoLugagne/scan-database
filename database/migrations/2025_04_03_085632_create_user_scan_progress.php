<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Scan;
use App\Models\UserScanProgress;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, store the existing data
        $scanData = Scan::all();

        // Create the new table
        Schema::create('user_scan_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('scan_id')->constrained('scans')->onDelete('cascade');
            $table->float('current_chapter')->nullable();
            $table->timestamps();
        });

        // Migrate the data to the new table
        foreach ($scanData as $scan) {
            UserScanProgress::create([
                'user_id' => $scan->user_id,
                'scan_id' => $scan->id,
                'current_chapter' => $scan->current_chapter,
                'created_at' => $scan->create_date,
                'updated_at' => $scan->last_update,
            ]);
        }

        // Store timestamps before dropping columns
        $timestamps = $scanData->mapWithKeys(function ($scan) {
            return [$scan->id => [
                'created_at' => $scan->create_date,
                'updated_at' => $scan->last_update,
            ]];
        });

        // Modify the scans table
        Schema::table('scans', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id','title']);
            $table->unique(['title']);
            // Then drop the columns
            $table->dropColumn(['user_id', 'current_chapter', 'last_update', 'create_date']);
            // Finally add timestamps
            $table->timestamps();
        });

        // Update timestamps after adding the new columns
        foreach ($timestamps as $id => $dates) {
            Scan::where('id', $id)->update($dates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, get all the progress data
        $progressData = UserScanProgress::with('scan')->get();

        // Add back the original columns
        Schema::table('scans', function (Blueprint $table) {
            // First drop timestamps
            $table->dropTimestamps();
            // Then add back the original columns
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->float('current_chapter')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('create_date')->nullable();
            // Add back the unique constraint
            $table->dropUnique(['title']);
            $table->unique(['user_id', 'title']);
        });

        // Restore the data
        foreach ($progressData as $progress) {
            Scan::where('id', $progress->scan_id)->update([
                'user_id' => $progress->user_id,
                'current_chapter' => $progress->current_chapter,
                'last_update' => $progress->updated_at,
                'create_date' => $progress->created_at,
            ]);
        }

        // Drop the progress table
        Schema::dropIfExists('user_scan_progress');
    }
};
