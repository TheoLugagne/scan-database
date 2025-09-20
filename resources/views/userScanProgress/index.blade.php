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

    <div class="max-w-7xl py-8 bg-gray-900 rounded-lg shadow-xl mx-auto sm:px-6 lg:px-8">
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
            @include('userScanProgress.partials.scan-progress-list')
        </div>
    </div>

    <!-- Replace the existing modal with the component -->
    <x-delete-modal />

    @push('scripts')
    <script>
        let searchTimeout;
        const searchInput = document.getElementById('search');
        const searchButton = document.getElementById('search-button');
        const searchIcon = document.getElementById('search-icon');
        const clearIcon = document.getElementById('clear-icon');
        const perPageInput = document.getElementById('per_page');
        const scansContainer = document.getElementById('scans-container');

        // Function to update URL parameters
        function updateUrlParams(params) {
            const url = new URL(window.location.href);
            Object.entries(params).forEach(([key, value]) => {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });
            window.history.pushState({}, '', url);
        }

        // Function to get current page from URL
        function getCurrentPage() {
            const params = new URLSearchParams(window.location.search);
            return parseInt(params.get('page')) || 1;
        }

        // Function to fetch scans with current parameters
        function fetchScans() {
            const params = new URLSearchParams(window.location.search);
            const search = searchInput.value;
            const perPage = perPageInput.value;
            const currentPage = getCurrentPage();
            
            if (search) params.set('search', search);
            else params.delete('search');
            
            params.set('per_page', perPage);
            params.set('page', currentPage);
            
            // Update URL without triggering a page reload
            updateUrlParams({
                search: search || null,
                per_page: perPage || 12,
                page: currentPage || 1
            });

            // Fetch new content
            fetch(`{{ route('userScanProgress.fetch') }}?${params.toString()}`)
                .then(response => response.text())
                .then(html => {
                    scansContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching scans:', error);
                    scansContainer.innerHTML = '<div class="text-center py-8"><p class="text-red-400">Error loading scans. Please try again.</p></div>';
                });
        }

        // Function to handle page navigation
        function gotoPage(value) {
            value = Math.min(Math.max(1, parseInt(value) || 1), parseInt(document.getElementById('goto-page').max));
            updateUrlParams({ page: value });
            fetchScans();
        }

        // Handle search input with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Reset to first page when searching
                updateUrlParams({ page: 1 });
                fetchScans();
            }, 500);

            // Toggle clear button visibility
            if (this.value) {
                searchIcon.classList.add('hidden');
                clearIcon.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIcon.classList.add('hidden');
            }
        });

        // Handle clear search button
        searchButton.addEventListener('click', function() {
            if (searchInput.value) {
                searchInput.value = '';
                searchIcon.classList.remove('hidden');
                clearIcon.classList.add('hidden');
                // Reset to first page when clearing search
                updateUrlParams({ page: 1 });
                fetchScans();
            }
        });

        // Handle per page input
        perPageInput.addEventListener('change', function() {
            // Ensure value is at least 1
            this.value = Math.max(1, parseInt(this.value) || 12);
            // Reset to first page when changing items per page
            updateUrlParams({ page: 1 });
            fetchScans();
        });

        // Handle pagination clicks
        document.addEventListener('click', function(event) {
            const link = event.target.closest('[data-page-url]');
            if (link) {
                event.preventDefault();
                const url = new URL(link.dataset.pageUrl);
                const params = new URLSearchParams(url.search);
                const page = params.get('page');
                
                // Preserve current search and per_page values
                if (searchInput.value) params.set('search', searchInput.value);
                params.set('per_page', perPageInput.value);
                
                // Update URL and fetch
                updateUrlParams({
                    page: page,
                    search: searchInput.value || null,
                    per_page: perPageInput.value
                });
                
                fetchScans();
            }
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            searchInput.value = params.get('search') || '';
            perPageInput.value = params.get('per_page') || 12;
            
            // Update search icon state
            if (searchInput.value) {
                searchIcon.classList.add('hidden');
                clearIcon.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIcon.classList.add('hidden');
            }
            
            fetchScans();
        });

        // Initialize page from URL on load
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const page = params.get('page');
            if (page) {
                updateUrlParams({ page: page });
            }
        });
    </script>
    @endpush
</x-app-layout> 
