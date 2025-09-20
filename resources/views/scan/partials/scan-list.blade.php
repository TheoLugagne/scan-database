@if($scans->isEmpty())
    <div class="text-center py-8">
        <p class="text-gray-400">No scans found.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($scans as $scan)
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                @if($scan->cover_image)
                    <div class="relative w-full h-64">
                        <img src="{{ asset('storage/' . $scan->cover_image) }}"
                            alt="{{ $scan->title }}"
                            class="absolute inset-0 w-full h-full object-contain bg-gray-700">
                    </div>
                @endif
                <div class="p-6">
                    <a href="{{ route('scan.show', $scan) }}" class="text-xl font-semibold text-white mb-2 hover:text-indigo-400">
                        {{ $scan->title }}
                    </a>
                    @if($scan->summary)
                        <p class="text-gray-400 text-sm mb-4 line-clamp-3">{{ $scan->summary }}</p>
                    @endif
                    @if($scan->link_to_scan)
                        <div class="flex items-center justify-between text-sm text-gray-400">
                            <a href="{{ $scan->link_to_scan }}"
                                target="_blank"
                                class="inline-flex items-center text-sm text-indigo-400 hover:text-indigo-300">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                View Scan
                            </a>
                        </div>
                    @endif
                    <div class="mt-4 flex justify-end space-x-2">
                        @if(auth()->check() && !auth()->user()->scans()->where('scan_id', $scan->id)->exists())
                        <a href="{{ route('userScanProgress.create', $scan) }}"
                            class="text-gray-400 hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </a>
                        @endif
                        <a href="{{ route('scan.edit', $scan) }}"
                            class="text-gray-400 hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <!-- <button type="button"
                            onclick="showDeleteScanModal('{{ $scan->id }}', '{{ $scan->title }}')"
                            class="text-gray-400 hover:text-red-400 cursor-pointer">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button> -->
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $scans->links('scan.pagination.scan-pagination') }}
    </div>
@endif 