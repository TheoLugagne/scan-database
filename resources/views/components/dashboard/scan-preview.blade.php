@props(['scan', 'href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex min-w-0 flex-1 items-center gap-2 text-white hover:text-indigo-400']) }}>
    <span class="relative h-20 w-16 shrink-0 overflow-hidden rounded bg-gray-700">
        @if ($scan?->cover_image)
            <img
                src="{{ asset('storage/' . $scan->cover_image) }}"
                alt=""
                class="absolute inset-0 h-full w-full object-contain"
            >
        @endif
    </span>
    <span class="min-w-0">
        <span class="block truncate font-medium">{{ $scan?->title }}</span>
        @if (filled($scan?->summary))
            <span class="mt-1 line-clamp-3 font-normal text-gray-400">{{ $scan->summary }}</span>
        @endif
    </span>
</a>
