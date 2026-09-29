<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-white">
                {{ $userScanProgress->scan->title }}
            </h2>
            <x-m2o.pill :elt="$userScanProgress->scan->status" />
        </div>
    </x-slot>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="border-b border-gray-700 pb-4">
                    <x-scan.current-chapter :progress="$userScanProgress" />
                </div>

                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm mb-2">Available chapters</h3>
                    <x-scan.available-chapters :scan="$userScanProgress->scan" />
                </div>

                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm">Link to Scan</h3>
                    @if($userScanProgress->scan->link_to_scan)
                        <a href="{{ $userScanProgress->scan->link_to_scan }}" class="text-blue-400 hover:text-blue-300" target="_blank">
                            View Original Scan
                        </a>
                    @else
                        <p class="text-gray-500">No link available</p>
                    @endif
                </div>

                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm">Summary</h3>
                    @if($userScanProgress->scan->summary)
                        <p class="text-white">{{ $userScanProgress->scan->summary }}</p>
                    @else
                        <p class="text-gray-500">No summary available</p>
                    @endif
                </div>

                {{-- Add genres and status display --}}
                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm mb-2">Genres</h3>
                    <x-m2m.pills :elts="$userScanProgress->scan->genres" :title="'Genres'"/>
                </div>

                <div class="pt-4 flex space-x-4">
                    <a href="{{ route('userScanProgress.edit', $userScanProgress) }}" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Edit Scan
                    </a>
                    <button onclick="showDeleteModal('{{ $userScanProgress->id }}', '{{ $userScanProgress->scan->title }}')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded cursor-pointer">
                        Delete Scan
                    </button>
                    <a href="{{ route('userScanProgress.index') }}" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                        Back to List
                    </a>
                </div>
            </div>


            <div class="flex flex-col h-full">
                <x-m2o.statusbar :elts="$reading_status_list" :selected="$userScanProgress->reading_status" :readonly="true" />
                <div class="bg-gray-900 rounded-lg p-6 flex-1">
                    @if($userScanProgress->scan->cover_image)
                        <h3 class="text-white text-xl mb-4">Cover Image</h3>
                        <img src="{{ asset('storage/' . $userScanProgress->scan->cover_image) }}" 
                            alt="Cover Image" 
                            class="max-w-full h-48 object-contain rounded">
                    @else
                        <p class="text-gray-400">No cover image available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-delete-modal />

    @push('scripts')
        @include('scan.partials.chapter-editor-script')
    @endpush
</x-app-layout>