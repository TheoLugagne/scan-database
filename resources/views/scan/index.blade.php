<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Left side with responsive width --}}
            <div class="w-24 sm:w-48">
                <h2 class="font-semibold text-xl text-white whitespace-nowrap">
                    {{ __('My Scans') }}
                </h2>
            </div>
            
            {{-- Centered Search Bar (wider on small screens) --}}
            <div class="flex-1 flex justify-center">
                <div class="relative w-full max-w-md sm:max-w-xl">
                    <input type="text" 
                           id="search" 
                           class="w-full bg-gray-800 border border-gray-600 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 pr-10" 
                           placeholder="Search">
                    <button type="button" 
                            id="search-button"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-white">
                        <svg id="search-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <svg id="clear-icon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Right side with responsive width --}}
            <div class="w-24 sm:w-48 flex justify-end">
                <div class="flex items-center space-x-2 whitespace-nowrap">
                    <span class="hidden sm:inline text-sm text-gray-300">Items per page:</span>
                    <input type="number" 
                           id="per_page" 
                           value="{{ request('per_page', 12) }}"
                           min="1"
                           title="Items per page"
                           class="bg-gray-800 border border-gray-600 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-16 p-2.5 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Add Scan Button --}}
            <div class="mb-6">
                <a href="{{ route('scan.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Scan
                </a>
            </div>

            {{-- Scans Grid --}}
            <div id="scans-container">
                @include('scan.partials.scan-list')
            </div>
        </div>
    </div>

    <!-- Replace the existing modal with the component -->
    <x-delete-modal />

    @push('scripts')
    <script>
        function fetchScans(url = null) {
            const perPage = document.getElementById('per_page').value || 12;
            const search = document.getElementById('search').value || '';
            const params = new URLSearchParams(window.location.search);
            
            params.set('per_page', perPage);
            params.set('search', search);

            document.getElementById('scans-container').classList.add('opacity-50');
            
            const fetchUrl = url || ('{{ route('scans.fetch') }}?' + params.toString());
            
            fetch(fetchUrl)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('scans-container').innerHTML = html;
                    
                    // Reattach event listeners after content update
                    document.querySelectorAll('[data-page-url]').forEach(button => {
                        button.addEventListener('click', handlePaginationClick);
                    });
                    
                    window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
                })
                .finally(() => {
                    document.getElementById('scans-container').classList.remove('opacity-50');
                });
        }

        function updateInput(per_page_input, search_input) {
            per_page_value = Math.max(1, parseInt(per_page_input.value) || 12);
            per_page_input.value = per_page_value;
            per_page_input.blur();
            search_input.blur();

            const params = new URLSearchParams(window.location.search);
            params.set('per_page', per_page_value);
            params.set('page', '1'); // Reset to first page
            params.set('search', search_input.value);
            fetchScans('{{ route('scans.fetch') }}?' + params.toString());
        }

        // Handle both change and Enter key
        const perPageInput = document.getElementById('per_page');
        const searchInput = document.getElementById('search');

        perPageInput.addEventListener('change', function() {
            const searchInput = document.getElementById('search');
            updateInput(this, searchInput);
        });
        perPageInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                const searchInput = document.getElementById('search');
                updateInput(this, searchInput);
            }
        });
        searchInput.addEventListener('change', function() {
            const perPageInput = document.getElementById('per_page');
            updateInput(perPageInput, this)
        })
        searchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                const perPageInput = document.getElementById('per_page');
                updateInput(perPageInput, this)
            }
        });

        // Initial event listeners setup
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-page-url]').forEach(button => {
                button.addEventListener('click', handlePaginationClick);
            });
        });
        
        const searchButton = document.getElementById('search-button');
        const searchIcon = document.getElementById('search-icon');
        const clearIcon = document.getElementById('clear-icon');

        // Function to toggle icons based on search input
        function toggleSearchIcon() {
            if (searchInput.value.length > 0) {
                searchIcon.classList.add('hidden');
                clearIcon.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIcon.classList.add('hidden');
            }
        }

        // Function to perform search
        function performSearch() {
            const params = new URLSearchParams(window.location.search);
            if (searchInput.value.trim()) {
                params.set('search', searchInput.value.trim());
            } else {
                params.delete('search');
            }
            params.set('page', '1'); // Reset to first page on search
            fetchScans('{{ route('scans.fetch') }}?' + params.toString());
        }

        // Handle input changes
        searchInput.addEventListener('input', toggleSearchIcon);

        // Handle enter key
        searchInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        });

        // Handle button click
        searchButton.addEventListener('click', function() {
            if (searchInput.value.length > 0) {
                // Clear search if there's text
                searchInput.value = '';
                toggleSearchIcon();
                performSearch();
            } else {
                // Perform search if empty
                performSearch();
            }
        });

        // Initialize icon state
        toggleSearchIcon();
    </script>
    @endpush
</x-app-layout> 
