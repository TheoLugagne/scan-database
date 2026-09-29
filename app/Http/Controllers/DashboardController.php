<?php

namespace App\Http\Controllers;

use App\Models\ReadingStatus;
use App\Models\Scan;
use App\Models\ScanStatus;
use App\Models\User;
use App\Models\UserScanProgress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const LIST_LIMIT = 8;

    private const SHORT_LIST_LIMIT = 5;

    private const STALE_CHAPTER_DAYS = 30;

    public function index(Request $request): View
    {
        $user = $request->user();

        $progress = UserScanProgress::query()
            ->where('user_id', $user->id)
            ->with('scan')
            ->get();

        $data = [
            'library' => $this->libraryStats($progress),
            'chaptersBehind' => $this->chaptersBehind($progress),
            'blockedReads' => $this->blockedReads($progress),
            'recentlyUpdated' => $this->recentlyUpdated($progress),
            'longestSinceUpdate' => $this->longestSinceUpdate($progress),
        ];

        if ($this->isAdmin($user)) {
            $data['incompleteScans'] = $this->incompleteScans();
            $data['catalog'] = $this->catalogStats();
            $data['staleChapterCounts'] = $this->staleChapterCounts();
        }

        return view('dashboard', $data);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * @param  Collection<int, UserScanProgress>  $progress
     * @return array{total: int, by_status: array<string, int>}
     */
    private function libraryStats(Collection $progress): array
    {
        $byStatus = [];

        foreach (ReadingStatus::all() as $status) {
            $byStatus[$status->value] = $progress->where('reading_status', $status)->count();
        }

        return [
            'total' => $progress->count(),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Reads whose published chapter count is ahead of the reader's current chapter.
     *
     * @param  Collection<int, UserScanProgress>  $progress
     * @return array{count: int, total_gap: int|float, items: Collection<int, UserScanProgress>}
     */
    private function chaptersBehind(Collection $progress): array
    {
        $behind = $progress
            ->filter(function (UserScanProgress $row) {
                if ($row->current_chapter === null) {
                    return false;
                }

                $available = $row->scan?->available_chapters;

                return $available !== null && $available > $row->current_chapter;
            })
            ->sort(function (UserScanProgress $a, UserScanProgress $b) {
                $gapCompare = $this->chapterGap($b) <=> $this->chapterGap($a);

                return $gapCompare !== 0
                    ? $gapCompare
                    : strcasecmp((string) $a->scan?->title, (string) $b->scan?->title);
            })
            ->values();

        return [
            'count' => $behind->count(),
            'total_gap' => $behind->sum(fn (UserScanProgress $row) => $this->chapterGap($row)),
            'items' => $behind->take(self::LIST_LIMIT)->values(),
        ];
    }

    private function chapterGap(UserScanProgress $row): int|float
    {
        return $row->scan->available_chapters - $row->current_chapter;
    }

    /**
     * Reads whose scan is on hiatus or cancelled.
     *
     * @param  Collection<int, UserScanProgress>  $progress
     * @return array{count: int, items: Collection<int, UserScanProgress>}
     */
    private function blockedReads(Collection $progress): array
    {
        $blocked = $progress
            ->filter(function (UserScanProgress $row) {
                return in_array($row->scan?->status, [ScanStatus::HIATUS, ScanStatus::CANCELLED], true);
            })
            ->sortBy(fn (UserScanProgress $row) => mb_strtolower((string) $row->scan?->title))
            ->values();

        return $this->listPayload($blocked, self::LIST_LIMIT);
    }

    /**
     * @param  Collection<int, UserScanProgress>  $progress
     * @return array{count: int, items: Collection<int, UserScanProgress>}
     */
    private function recentlyUpdated(Collection $progress): array
    {
        $recent = $progress
            ->sort(function (UserScanProgress $a, UserScanProgress $b) {
                $byDate = $b->updated_at <=> $a->updated_at;

                return $byDate !== 0 ? $byDate : $b->id <=> $a->id;
            })
            ->values();

        return $this->listPayload($recent, self::SHORT_LIST_LIMIT);
    }

    /**
     * Ongoing, not-started, and on-hold rows left untouched the longest.
     * Completed and dropped reads are omitted so the list stays actionable.
     *
     * @param  Collection<int, UserScanProgress>  $progress
     * @return array{count: int, items: Collection<int, UserScanProgress>}
     */
    private function longestSinceUpdate(Collection $progress): array
    {
        $actionable = $progress
            ->filter(fn (UserScanProgress $row) => in_array($row->reading_status, [
                ReadingStatus::ONGOING,
                ReadingStatus::NOT_STARTED,
                ReadingStatus::ON_HOLD,
            ], true))
            ->sort(function (UserScanProgress $a, UserScanProgress $b) {
                $byDate = $a->updated_at <=> $b->updated_at;

                return $byDate !== 0 ? $byDate : $a->id <=> $b->id;
            })
            ->values();

        return $this->listPayload($actionable, self::SHORT_LIST_LIMIT);
    }

    /**
     * @return array{count: int, items: Collection<int, Scan>}
     */
    private function incompleteScans(): array
    {
        $items = Scan::incomplete()
            ->orderBy('title')
            ->limit(self::LIST_LIMIT)
            ->get();

        return [
            'count' => Scan::incomplete()->count(),
            'items' => $items,
        ];
    }

    /**
     * @return array{total: int, by_status: array<string, int>}
     */
    private function catalogStats(): array
    {
        $grouped = Scan::query()
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byStatus = [];

        foreach (ScanStatus::all() as $status) {
            $byStatus[$status->value] = (int) ($grouped[$status->value] ?? 0);
        }

        return [
            'total' => Scan::count(),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Chapter counts that were never recorded, or last recorded more than 30 days ago.
     *
     * @return array{count: int, items: Collection<int, Scan>}
     */
    private function staleChapterCounts(): array
    {
        $cutoff = now()->subDays(self::STALE_CHAPTER_DAYS);

        $query = Scan::query()
            ->whereNotNull('available_chapters')
            ->where(function (Builder $query) use ($cutoff) {
                $query->whereNull('available_chapters_updated_at')
                    ->orWhere('available_chapters_updated_at', '<', $cutoff);
            });

        return [
            'count' => (clone $query)->count(),
            'items' => (clone $query)
                ->orderByRaw('available_chapters_updated_at is null desc')
                ->orderBy('available_chapters_updated_at')
                ->orderBy('title')
                ->limit(self::LIST_LIMIT)
                ->get(),
        ];
    }

    /**
     * @param  Collection<int, mixed>  $rows
     * @return array{count: int, items: Collection<int, mixed>}
     */
    private function listPayload(Collection $rows, int $limit): array
    {
        return [
            'count' => $rows->count(),
            'items' => $rows->take($limit)->values(),
        ];
    }
}
