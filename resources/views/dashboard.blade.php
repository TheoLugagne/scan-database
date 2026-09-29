<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-dashboard.panel title="My library" class="h-full">
                    <p class="text-4xl font-semibold text-white tabular-nums">{{ $library['total'] }}</p>
                    <p class="mt-1">tracked {{ \Illuminate\Support\Str::plural('scan', $library['total']) }}</p>
                    <dl class="mt-4 space-y-1">
                        @foreach (\App\Models\ReadingStatus::all() as $status)
                            <div class="flex items-center justify-between gap-4">
                                <dt>{{ $status->label() }}</dt>
                                <dd class="font-medium text-white tabular-nums">{{ $library['by_status'][$status->value] ?? 0 }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </x-dashboard.panel>

                <x-dashboard.panel title="Chapters behind" class="h-full">
                    <p class="text-4xl font-semibold text-white tabular-nums">{{ $chaptersBehind['total_gap'] }}</p>
                    <p class="mt-1">{{ \Illuminate\Support\Str::plural('chapter', $chaptersBehind['total_gap']) }} to catch up</p>
                    <p class="mt-4">
                        Across {{ $chaptersBehind['count'] }} {{ \Illuminate\Support\Str::plural('read', $chaptersBehind['count']) }}
                    </p>
                </x-dashboard.panel>

                <x-dashboard.panel title="Blocked reads" class="h-full">
                    <p class="text-4xl font-semibold text-white tabular-nums">{{ $blockedReads['count'] }}</p>
                    <p class="mt-1">{{ \Illuminate\Support\Str::plural('read', $blockedReads['count']) }} on hiatus or cancelled</p>
                </x-dashboard.panel>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-dashboard.panel title="Chapters behind" class="h-full">
                    @if ($chaptersBehind['items']->isEmpty())
                        <p>No reads are behind the latest chapter.</p>
                    @else
                        <ul class="divide-y divide-gray-700">
                            @foreach ($chaptersBehind['items'] as $progress)
                                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <x-dashboard.scan-preview :scan="$progress->scan" :href="route('userScanProgress.show', $progress)" />
                                    <span class="shrink-0 tabular-nums">
                                        Ch. {{ $progress->current_chapter }} / {{ $progress->scan->available_chapters }}
                                        · {{ $progress->scan->available_chapters - $progress->current_chapter }} behind
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        @if ($chaptersBehind['count'] > $chaptersBehind['items']->count())
                            <a href="{{ route('userScanProgress.index') }}" class="mt-4 inline-block font-medium text-indigo-400 hover:text-indigo-300">
                                View all {{ $chaptersBehind['count'] }}
                            </a>
                        @endif
                    @endif
                </x-dashboard.panel>

                <x-dashboard.panel title="Blocked reads" class="h-full">
                    @if ($blockedReads['items']->isEmpty())
                        <p>No reads are on hiatus or cancelled.</p>
                    @else
                        <ul class="divide-y divide-gray-700">
                            @foreach ($blockedReads['items'] as $progress)
                                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <x-dashboard.scan-preview :scan="$progress->scan" :href="route('userScanProgress.show', $progress)" />
                                    <span class="shrink-0 text-right">
                                        {{ $progress->scan->status?->label() }}
                                        <span class="mt-1 block">{{ $progress->updated_at->diffForHumans() }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        @if ($blockedReads['count'] > $blockedReads['items']->count())
                            <a href="{{ route('userScanProgress.index') }}" class="mt-4 inline-block font-medium text-indigo-400 hover:text-indigo-300">
                                View all {{ $blockedReads['count'] }}
                            </a>
                        @endif
                    @endif
                </x-dashboard.panel>

                <x-dashboard.panel title="Recently updated" class="h-full">
                    @if ($recentlyUpdated['items']->isEmpty())
                        <p>No tracked scans yet.</p>
                    @else
                        <ul class="divide-y divide-gray-700">
                            @foreach ($recentlyUpdated['items'] as $progress)
                                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <x-dashboard.scan-preview :scan="$progress->scan" :href="route('userScanProgress.show', $progress)" />
                                    <span class="shrink-0">{{ $progress->updated_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if ($recentlyUpdated['count'] > $recentlyUpdated['items']->count())
                            <a href="{{ route('userScanProgress.index') }}" class="mt-4 inline-block font-medium text-indigo-400 hover:text-indigo-300">
                                View all {{ $recentlyUpdated['count'] }}
                            </a>
                        @endif
                    @endif
                </x-dashboard.panel>

                <x-dashboard.panel title="Longest since last update" class="h-full">
                    @if ($longestSinceUpdate['items']->isEmpty())
                        <p>No ongoing, unread, or paused scans.</p>
                    @else
                        <ul class="divide-y divide-gray-700">
                            @foreach ($longestSinceUpdate['items'] as $progress)
                                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <x-dashboard.scan-preview :scan="$progress->scan" :href="route('userScanProgress.show', $progress)" />
                                    <span class="shrink-0">{{ $progress->updated_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if ($longestSinceUpdate['count'] > $longestSinceUpdate['items']->count())
                            <a href="{{ route('userScanProgress.index') }}" class="mt-4 inline-block font-medium text-indigo-400 hover:text-indigo-300">
                                View all {{ $longestSinceUpdate['count'] }}
                            </a>
                        @endif
                    @endif
                </x-dashboard.panel>
            </div>
        </div>

        @isset($incompleteScans, $catalog, $staleChapterCounts)
            <div class="space-y-6 border-t border-gray-700 pt-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-400">Admin</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <x-dashboard.panel title="Incomplete scans" :superuser="true" class="h-full">
                        <p class="text-4xl font-semibold text-white tabular-nums">{{ $incompleteScans['count'] }}</p>
                        <p class="mt-1">{{ \Illuminate\Support\Str::plural('scan', $incompleteScans['count']) }} missing information</p>
                    </x-dashboard.panel>

                    <x-dashboard.panel title="Catalog" :superuser="true" class="h-full">
                        <p class="text-4xl font-semibold text-white tabular-nums">{{ $catalog['total'] }}</p>
                        <p class="mt-1">{{ \Illuminate\Support\Str::plural('scan', $catalog['total']) }} in the catalog</p>
                        <dl class="mt-4 space-y-1">
                            @foreach (\App\Models\ScanStatus::all() as $status)
                                <div class="flex items-center justify-between gap-4">
                                    <dt>{{ $status->label() }}</dt>
                                    <dd class="font-medium text-white tabular-nums">{{ $catalog['by_status'][$status->value] ?? 0 }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </x-dashboard.panel>

                    <x-dashboard.panel title="Stale chapter counts" :superuser="true" class="h-full">
                        <p class="text-4xl font-semibold text-white tabular-nums">{{ $staleChapterCounts['count'] }}</p>
                        <p class="mt-1">not updated in the last 30 days</p>
                    </x-dashboard.panel>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <x-dashboard.panel title="Incomplete scans" :superuser="true" class="h-full">
                        @if ($incompleteScans['items']->isEmpty())
                            <p>Every scan has complete information.</p>
                        @else
                            <ul class="divide-y divide-gray-700">
                                @foreach ($incompleteScans['items'] as $scan)
                                    <li class="py-3">
                                        <x-dashboard.scan-preview :scan="$scan" :href="route('scan.edit', $scan)" />
                                    </li>
                                @endforeach
                            </ul>
                            @if ($incompleteScans['count'] > $incompleteScans['items']->count())
                                <a href="{{ route('scan.index') }}" class="mt-4 inline-block font-medium text-indigo-400 hover:text-indigo-300">
                                    View all {{ $incompleteScans['count'] }}
                                </a>
                            @endif
                        @endif
                    </x-dashboard.panel>

                    <x-dashboard.panel title="Stale chapter counts" :superuser="true" class="h-full">
                        @if ($staleChapterCounts['items']->isEmpty())
                            <p>Chapter counts are up to date.</p>
                        @else
                            <ul class="divide-y divide-gray-700">
                                @foreach ($staleChapterCounts['items'] as $scan)
                                    <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                                        <x-dashboard.scan-preview :scan="$scan" :href="route('scan.edit', $scan)" />
                                        <span class="shrink-0">
                                            @if ($scan->available_chapters_updated_at)
                                                {{ $scan->available_chapters_updated_at->format('M d, Y') }}
                                            @else
                                                Never updated
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($staleChapterCounts['count'] > $staleChapterCounts['items']->count())
                                <a href="{{ route('scan.index') }}" class="mt-4 inline-block font-medium text-indigo-400 hover:text-indigo-300">
                                    View all {{ $staleChapterCounts['count'] }}
                                </a>
                            @endif
                        @endif
                    </x-dashboard.panel>
                </div>
            </div>
        @endisset
    </div>
</x-app-layout>
