<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            // Remove the old unique constraint
            $table->dropUnique(['title']);
            // Add new composite unique constraint
            $table->unique(['user_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Check if the unique constraint exists before trying to drop it
            $indexes = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND sql LIKE '%UNIQUE%' AND tbl_name='scans'");
            $hasUniqueIndex = collect($indexes)->contains(function ($index) {
                return str_contains($index->name, 'user_id') && str_contains($index->name, 'title');
            });
            
            if ($hasUniqueIndex) {
                $table->dropUnique(['user_id', 'title']);
            }
            
            // Finally drop the column
            $table->dropColumn('user_id');
            // Restore the original unique constraint
            $table->unique(['title']);
        });
    }
}; 