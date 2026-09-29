<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white">
                {{ $scan->title }}
            </h2>
            <x-m2o.pill :elt="$scan->status" />
        </div>
    </x-slot>

    <div class="container mx-auto px-4">
        <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-6xl mx-auto">
            <div class="flex flex-col items-center">
                <div class="w-full max-w-3xl">
                    <div class="bg-gray-900 rounded-lg p-6 flex flex-col items-center mb-6">
                        @if($scan->cover_image)
                            <h3 class="text-white text-xl mb-4 text-center">Cover Image</h3>
                            <img src="{{ asset('storage/' . $scan->cover_image) }}" 
                                alt="Cover Image" 
                                class="max-w-full h-64 object-contain rounded">
                        @else
                            <p class="text-gray-400 text-center">No cover image available</p>
                        @endif
                    </div>
                            
                    <div class="border-b border-gray-700 pb-4 mb-6 text-center">
                        <h3 class="text-gray-400 text-sm mb-2">Summary</h3>
                        @if($scan->summary)
                            <p class="text-white">{{ $scan->summary }}</p>
                        @else
                            <p class="text-gray-500">No summary available</p>
                        @endif
                    </div>

                    <div class="border-b border-gray-700 pb-4 mb-6 flex flex-col items-center">
                        <h3 class="text-gray-400 text-sm mb-2">Available chapters</h3>
                        <x-scan.available-chapters :scan="$scan" class="justify-center" />
                    </div>

                    {{-- Genre Pills --}}
                    <div class="border-b border-gray-700 pb-4 mb-6 text-center flex flex-col items-center">
                        <h3 class="text-gray-400 text-sm mb-2">Genres</h3>
                        <x-m2m.pills :elts="$scan->genres" :title="'Genres'"/>
                    </div>
                    
                    <div class="border-b border-gray-700 pb-4 mb-6 text-center">
                        <h3 class="text-gray-400 text-sm mb-2">Link to Scan</h3>
                        @if($scan->link_to_scan)
                            <a href="{{ $scan->link_to_scan }}" class="text-blue-400 hover:text-blue-300" target="_blank">
                                View Original Scan
                            </a>
                        @else
                            <p class="text-gray-500">No link available</p>
                        @endif
                    </div>

                    <div class="pt-4 flex justify-center space-x-4">
                        <a href="{{ route('userScanProgress.create', $scan) }}" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                            Add to My Scans
                        </a>
                        <a href="{{ route('scan.edit', $scan) }}" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                            Edit Scan
                        </a>
                        <!-- <button onclick="showDeleteModal('{{ $scan->id }}', '{{ $scan->title }}')"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded cursor-pointer">
                            Delete Scan
                        </button> -->
                        <a href="{{ route('scan.index') }}" 
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-delete-modal />
</x-app-layout>