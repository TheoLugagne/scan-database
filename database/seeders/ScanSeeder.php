<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\models\Scan;

class ScanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scan::factory()->create([
            'title' => 'Test Scan',
            'summary' => 'This is a test scan for demonstration purposes.',
            'current_chapter' => 0,
            'cover_image' => 'https://example.com/test-scan-cover.jpg',
            'link_to_scan' => 'https://example.com/test-scan',
        ]);
    }
}
