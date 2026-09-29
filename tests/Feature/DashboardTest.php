<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\ReadingStatus;
use App\Models\Scan;
use App\Models\ScanStatus;
use App\Models\User;
use App\Models\UserScanProgress;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    private string|false $originalDatabase;

    protected function setUp(): void
    {
        $this->originalDatabase = getenv('DB_DATABASE');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_DATABASE'] = ':memory:';
        $_SERVER['DB_DATABASE'] = ':memory:';

        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-09-29 12:00:00'));

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

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

        Schema::create('user_scan_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scan_id')->constrained()->cascadeOnDelete();
            $table->float('current_chapter')->nullable();
            $table->string('reading_status')->default(ReadingStatus::ONGOING->value);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

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

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_builds_personal_stats_and_skips_admin_queries(): void
    {
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());

        $user = User::create([
            'name' => 'Reader',
            'email' => 'reader@example.com',
            'password' => 'password',
        ]);
        $someoneElse = User::create([
            'name' => 'Other',
            'email' => 'other@example.com',
            'password' => 'password',
        ]);

        $behind = [];
        foreach (range(1, 9) as $gap) {
            $behind[$gap] = $this->track($user, "Behind 0{$gap}", [
                'available_chapters' => 10 + $gap,
            ], [
                'current_chapter' => 10,
                'reading_status' => ReadingStatus::ONGOING,
                'updated_at' => now()->subHours($gap),
            ]);
        }

        $caughtUp = $this->track($user, 'Caught up', [
            'available_chapters' => 10,
        ], [
            'current_chapter' => 10,
            'reading_status' => ReadingStatus::ONGOING,
            'updated_at' => now()->subDays(80),
        ]);
        $unknownChapters = $this->track($user, 'Unknown chapters', [
            'available_chapters' => null,
        ], [
            'current_chapter' => 4,
            'reading_status' => ReadingStatus::ONGOING,
            'updated_at' => now()->subDays(70),
        ]);
        $unknownCurrent = $this->track($user, 'Unknown current', [
            'available_chapters' => 12,
        ], [
            'current_chapter' => null,
            'reading_status' => ReadingStatus::ONGOING,
            'updated_at' => now()->subDays(60),
        ]);
        $finished = $this->track($user, 'Finished', [
            'available_chapters' => 40,
        ], [
            'current_chapter' => 10,
            'reading_status' => ReadingStatus::COMPLETED,
            'updated_at' => now()->subDays(200),
        ]);
        $abandoned = $this->track($user, 'Abandoned', [
            'available_chapters' => 40,
        ], [
            'current_chapter' => 3,
            'reading_status' => ReadingStatus::DROPPED,
            'updated_at' => now()->subDays(180),
        ]);
        $shelf = $this->track($user, 'Shelf', [
            'available_chapters' => 8,
        ], [
            'current_chapter' => 0,
            'reading_status' => ReadingStatus::NOT_STARTED,
            'updated_at' => now()->subDays(90),
        ]);
        $hiatus = $this->track($user, 'Hiatus read', [
            'status' => ScanStatus::HIATUS,
            'available_chapters' => 5,
        ], [
            'current_chapter' => 5,
            'reading_status' => ReadingStatus::ONGOING,
            'updated_at' => now()->subDays(50),
        ]);
        $cancelled = $this->track($user, 'Cancelled read', [
            'status' => ScanStatus::CANCELLED,
            'available_chapters' => 3,
        ], [
            'current_chapter' => 3,
            'reading_status' => ReadingStatus::ONGOING,
            'updated_at' => now()->subDays(40),
        ]);
        $paused = $this->track($user, 'Paused hiatus', [
            'status' => ScanStatus::HIATUS,
            'available_chapters' => 9,
        ], [
            'current_chapter' => 2,
            'reading_status' => ReadingStatus::ON_HOLD,
            'updated_at' => now()->subDays(100),
        ]);

        $this->track($someoneElse, 'Not mine', [
            'available_chapters' => 100,
        ], [
            'current_chapter' => 1,
            'reading_status' => ReadingStatus::ONGOING,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $sql = strtolower(implode("\n", array_column(DB::getQueryLog(), 'query')));
        $this->assertStringNotContainsString('genre_scan', $sql);
        $this->assertStringNotContainsString('group by', $sql);
        $this->assertStringNotContainsString('available_chapters_updated_at', $sql);

        $response->assertOk()->assertViewIs('dashboard');
        $response->assertViewMissing('incompleteScans');
        $response->assertViewMissing('catalog');
        $response->assertViewMissing('staleChapterCounts');

        $response->assertViewHas('library', function (array $library) {
            return $library['total'] === 18
                && $library['by_status'] === [
                    'not_started' => 1,
                    'ongoing' => 14,
                    'completed' => 1,
                    'on_hold' => 1,
                    'dropped' => 1,
                ];
        });

        $response->assertViewHas('chaptersBehind', function (array $behindStats) use ($abandoned, $finished, $behind, $shelf, $paused) {
            $ids = $behindStats['items']->map(fn (UserScanProgress $row) => $row->id)->all();

            return $behindStats['count'] === 13
                && $behindStats['total_gap'] == 127
                && $ids === [
                    $abandoned->id,
                    $finished->id,
                    $behind[9]->id,
                    $behind[8]->id,
                    $shelf->id,
                    $behind[7]->id,
                    $paused->id,
                    $behind[6]->id,
                ];
        });

        $response->assertViewHas('blockedReads', function (array $blocked) use ($cancelled, $hiatus, $paused) {
            return $blocked['count'] === 3
                && $blocked['items']->map(fn (UserScanProgress $row) => $row->id)->all() === [
                    $cancelled->id,
                    $hiatus->id,
                    $paused->id,
                ];
        });

        $response->assertViewHas('recentlyUpdated', function (array $recent) use ($behind) {
            $ids = $recent['items']->map(fn (UserScanProgress $row) => $row->id)->all();

            return $recent['count'] === 18
                && $ids === [
                    $behind[1]->id,
                    $behind[2]->id,
                    $behind[3]->id,
                    $behind[4]->id,
                    $behind[5]->id,
                ];
        });

        $response->assertViewHas('longestSinceUpdate', function (array $neglected) use ($paused, $shelf, $caughtUp, $unknownChapters, $unknownCurrent) {
            $ids = $neglected['items']->map(fn (UserScanProgress $row) => $row->id)->all();

            return $neglected['count'] === 16
                && $ids === [
                    $paused->id,
                    $shelf->id,
                    $caughtUp->id,
                    $unknownChapters->id,
                    $unknownCurrent->id,
                ];
        });
    }

    public function test_admins_receive_catalog_stats_without_other_users_progress(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $reader = User::create([
            'name' => 'Reader',
            'email' => 'reader@example.com',
            'password' => 'password',
        ]);
        $genre = Genre::create(['name' => 'Action']);

        $mine = $this->makeScan('Alpha ongoing', [
            'status' => ScanStatus::ONGOING,
            'available_chapters' => 10,
            'available_chapters_updated_at' => now(),
        ]);
        $mine->genres()->attach($genre);
        $this->trackExisting($admin, $mine, [
            'current_chapter' => 4,
            'reading_status' => ReadingStatus::ONGOING,
        ]);

        $this->makeScan('Missing genres', [
            'status' => ScanStatus::COMPLETED,
            'available_chapters' => 5,
            'available_chapters_updated_at' => now(),
        ]);

        $neverUpdated = $this->makeScan('Never updated', [
            'status' => ScanStatus::HIATUS,
            'available_chapters' => 4,
            'available_chapters_updated_at' => null,
        ]);
        $neverUpdated->genres()->attach($genre);

        $oldCount = $this->makeScan('Old count', [
            'status' => ScanStatus::CANCELLED,
            'available_chapters' => 8,
            'available_chapters_updated_at' => now()->subDays(31),
        ]);
        $oldCount->genres()->attach($genre);

        $exactCutoff = $this->makeScan('Exact cutoff', [
            'status' => ScanStatus::ONGOING,
            'available_chapters' => 2,
            'available_chapters_updated_at' => now()->subDays(30),
        ]);
        $exactCutoff->genres()->attach($genre);

        $this->makeScan('Missing chapters', [
            'status' => ScanStatus::ONGOING,
            'available_chapters' => null,
            'available_chapters_updated_at' => now()->subDays(90),
        ])->genres()->attach($genre);

        $readerProgress = $this->track($reader, 'Reader only', [
            'available_chapters' => 50,
        ], [
            'current_chapter' => 1,
            'reading_status' => ReadingStatus::ONGOING,
        ]);
        $readerProgress->scan->genres()->attach($genre);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('library', fn (array $library) => $library['total'] === 1
            && $library['by_status']['ongoing'] === 1);

        $response->assertViewHas('incompleteScans', function (array $incomplete) {
            $titles = $incomplete['items']->pluck('title')->all();

            return $incomplete['count'] === 2
                && $titles === ['Missing chapters', 'Missing genres'];
        });

        $response->assertViewHas('catalog', function (array $catalog) {
            return $catalog['total'] === 7
                && $catalog['by_status'] === [
                    'ongoing' => 4,
                    'completed' => 1,
                    'hiatus' => 1,
                    'cancelled' => 1,
                ];
        });

        $response->assertViewHas('staleChapterCounts', function (array $stale) use ($neverUpdated, $oldCount) {
            return $stale['count'] === 2
                && $stale['items']->pluck('id')->all() === [$neverUpdated->id, $oldCount->id];
        });
    }

    /**
     * @param  array<string, mixed>  $scanOverrides
     * @param  array<string, mixed>  $progressOverrides
     */
    private function track(User $user, string $title, array $scanOverrides = [], array $progressOverrides = []): UserScanProgress
    {
        return $this->trackExisting($user, $this->makeScan($title, $scanOverrides), $progressOverrides);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function trackExisting(User $user, Scan $scan, array $overrides = []): UserScanProgress
    {
        $updatedAt = $overrides['updated_at'] ?? now();
        unset($overrides['updated_at']);

        $progress = UserScanProgress::create(array_merge([
            'user_id' => $user->id,
            'scan_id' => $scan->id,
            'current_chapter' => 1,
            'reading_status' => ReadingStatus::ONGOING,
        ], $overrides));

        $progress->updated_at = $updatedAt;
        $progress->save();

        return $progress->fresh('scan');
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
