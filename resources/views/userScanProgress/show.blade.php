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
                    <h3 class="text-gray-400 text-sm">Current Chapter</h3>
                    <div class="flex items-center space-x-2">
                        <input type="number" 
                            id="current_chapter" 
                            value="{{ $userScanProgress->current_chapter }}" 
                            step="0.1"
                            class="mt-1 block w-32 rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        
                        <button onclick="incrementChapter()"
                            class="mt-1 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded flex items-center justify-center w-10 h-10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>

                        <button onclick="updateChapter()"
                            class="mt-1 bg-green-600 hover:bg-green-700 text-white p-2 rounded flex items-center justify-center w-10 h-10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                    <div id="chapter-update-message" class="mt-2 text-sm hidden"></div>
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
    <script>
        function incrementChapter() {
            const input = document.getElementById('current_chapter');
            input.value = (parseFloat(input.value) + 1).toFixed(1);
        }

        function updateChapter() {
            const chapter = document.getElementById('current_chapter').value;
            const messageDiv = document.getElementById('chapter-update-message');
            
            const formData = new FormData();
            formData.append('current_chapter', chapter);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('userScanProgress.update-chapter', $userScanProgress) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                messageDiv.classList.remove('hidden');
                if (data.success) {
                    messageDiv.className = 'mt-2 text-sm text-green-500';
                    messageDiv.textContent = 'Chapter updated successfully';
                } else {
                    messageDiv.className = 'mt-2 text-sm text-red-500';
                    messageDiv.textContent = 'Error updating chapter';
                }
                
                setTimeout(() => {
                    messageDiv.classList.add('hidden');
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.classList.remove('hidden');
                messageDiv.className = 'mt-2 text-sm text-red-500';
                messageDiv.textContent = 'Error updating chapter';
                
                setTimeout(() => {
                    messageDiv.classList.add('hidden');
                }, 3000);
            });
        }
    </script>
    @endpush
</x-app-layout>