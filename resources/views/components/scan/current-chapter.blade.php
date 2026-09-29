@props(['progress'])

<div {{ $attributes->merge(['class' => 'w-full']) }} data-chapter-editor data-update-url="{{ route('userScanProgress.update-chapter', $progress) }}">
    <h3 class="text-gray-400 text-sm">Current Chapter</h3>
    <div class="flex w-full items-center gap-2">
        <input type="number"
            data-chapter-input
            value="{{ $progress->current_chapter }}"
            step="0.1"
            min="0"
            onkeydown="if (event.key === 'Enter') event.preventDefault()"
            class="mt-1 block min-w-0 flex-1 rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">

        <button type="button"
            data-chapter-action="increment"
            class="mt-1 shrink-0 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded flex items-center justify-center w-10 h-10">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>

        <button type="button"
            data-chapter-action="save"
            class="mt-1 shrink-0 bg-green-600 hover:bg-green-700 text-white p-2 rounded flex items-center justify-center w-10 h-10">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </button>
    </div>
    <div data-chapter-message class="mt-2 text-sm hidden"></div>
</div>
