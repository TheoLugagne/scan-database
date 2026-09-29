@props(['scan'])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-400']) }}>
    <span>
        @if($scan->available_chapters !== null)
            {{ $scan->available_chapters }} chapters available
        @else
            Chapters available: unknown
        @endif
    </span>
    @if($scan->available_chapters_updated_at)
        <span>Updated {{ $scan->available_chapters_updated_at->format('M d, Y') }}</span>
    @endif
    @if($scan->status !== \App\Models\ScanStatus::COMPLETED)
        <x-info-bubble />
    @endif
</div>
