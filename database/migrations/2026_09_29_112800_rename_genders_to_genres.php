<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fresh installs that already created genre_scan skip this. Existing
     * databases still have gender_scan, and those pivot rows are renamed
     * in place rather than recreated.
     */
    public function up(): void
    {
        if (Schema::hasTable('genre_scan') || ! Schema::hasTable('gender_scan')) {
            return;
        }

        // Constraint names stay gender_scan_* after a table rename, so drop
        // them while the pivot is still called gender_scan. scan_id and its
        // foreign key to scans are left in place.
        Schema::table('gender_scan', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropUnique(['gender_id', 'scan_id']);
        });

        Schema::rename('genders', 'genres');
        Schema::rename('gender_scan', 'genre_scan');

        Schema::table('genre_scan', function (Blueprint $table) {
            $table->renameColumn('gender_id', 'genre_id');
        });

        Schema::table('genre_scan', function (Blueprint $table) {
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
            $table->unique(['genre_id', 'scan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('gender_scan') || ! Schema::hasTable('genre_scan')) {
            return;
        }

        Schema::table('genre_scan', function (Blueprint $table) {
            $table->dropForeign(['genre_id']);
            $table->dropUnique(['genre_id', 'scan_id']);
        });

        Schema::table('genre_scan', function (Blueprint $table) {
            $table->renameColumn('genre_id', 'gender_id');
        });

        Schema::rename('genre_scan', 'gender_scan');
        Schema::rename('genres', 'genders');

        Schema::table('gender_scan', function (Blueprint $table) {
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
            $table->unique(['gender_id', 'scan_id']);
        });
    }
};
