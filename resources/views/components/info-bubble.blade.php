@props(['message' => 'The chapter count may not be accurate because the scan is not completed.'])

<span {{ $attributes->merge(['class' => 'relative inline-flex group']) }}>
    <button type="button"
        class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-500/20 text-xs font-semibold text-amber-300"
        aria-label="{{ $message }}">
        i
    </button>
    <span role="tooltip"
        class="pointer-events-none invisible absolute top-full left-1/2 z-20 mt-2 w-56 -translate-x-1/2 rounded-md bg-gray-700 px-3 py-2 text-xs font-normal text-white shadow-lg group-hover:visible group-focus-within:visible">
        {{ $message }}
    </span>
</span>
