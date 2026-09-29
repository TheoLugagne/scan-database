<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Scan;
use App\Models\ScanStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ScanIncompleteScopeTest extends TestCase
{
    private string|false $originalDatabase;

    protected function setUp(): void
    {
        $this->originalDatabase = getenv('DB_DATABASE');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();

        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->mediumText('summary')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('link_to_scan')->nullable();
            $table->string('status')->nullable();
            $table->unsignedInteger('available_chapters')->nullable();
            $table->timestamp('available_chapters_updated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('genre_scan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scan_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->originalDatabase === false) {
            putenv('DB_DATABASE');
            unset($_ENV['DB_DATABASE'], $_SERVER['DB_DATABASE']);
        } else {
            putenv('DB_DATABASE='.$this->originalDatabase);
            $_ENV['DB_DATABASE'] = $this->originalDatabase;
            $_SERVER['DB_DATABASE'] = $this->originalDatabase;
        }
    }

    public function test_incomplete_scope_matches_is_information_complete(): void
    {
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        $genre = Genre::create(['name' => 'Action']);

        $complete = $this->makeScan('Complete');
        $complete->genres()->attach($genre);

        $zeroChapters = $this->makeScan('Zero chapters', ['available_chapters' => 0]);
        $zeroChapters->genres()->attach($genre);

        $missingSummary = $this->makeScan('Missing summary', ['summary' => null]);
        $missingSummary->genres()->attach($genre);

        $blankSummary = $this->makeScan('Blank summary', ['summary' => '   ']);
        $blankSummary->genres()->attach($genre);

        $missingCover = $this->makeScan('Missing cover', ['cover_image' => null]);
        $missingCover->genres()->attach($genre);

        $blankLink = $this->makeScan('Blank link', ['link_to_scan' => '']);
        $blankLink->genres()->attach($genre);

        $missingStatus = $this->makeScan('Missing status', ['status' => null]);
        $missingStatus->genres()->attach($genre);

        $missingChapters = $this->makeScan('Missing chapters', ['available_chapters' => null]);
        $missingChapters->genres()->attach($genre);

        $this->makeScan('No genres');

        $incompleteIds = Scan::incomplete()->pluck('id');

        foreach (Scan::all() as $scan) {
            $this->assertSame(
                ! $scan->is_information_complete,
                $incompleteIds->contains($scan->id),
                $scan->title,
            );
        }

        $this->assertFalse($incompleteIds->contains($complete->id));
        $this->assertFalse($incompleteIds->contains($zeroChapters->id));
        $this->assertCount(7, $incompleteIds);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeScan(string $title, array $overrides = []): Scan
    {
        return Scan::create(array_merge([
            'title' => $title,
            'summary' => 'A summary',
            'cover_image' => 'cover.jpg',
            'link_to_scan' => 'https://example.com/scan',
            'status' => ScanStatus::ONGOING,
            'available_chapters' => 12,
            'available_chapters_updated_at' => now(),
        ], $overrides));
    }
}
