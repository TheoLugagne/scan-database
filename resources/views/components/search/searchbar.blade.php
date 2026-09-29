{{-- Centered search. The filter panel is anchored here so it hangs below the toolbar. --}}
<div class="flex-1 flex justify-center min-w-0">
    <div id="searchbar" class="relative w-full max-w-xl">
        <input type="text" 
                id="search" 
                value="{{ request('search', '') }}"
                class="w-full bg-gray-800 border border-gray-600 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 pr-10" 
                placeholder="Search">
        <button type="button" id="open-filter-panel" class="absolute inset-y-0 right-7 flex items-center px-3 text-gray-400 hover:text-white">
            <svg id="filter-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <svg id="close-filter-icon" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </button>
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
        <div class="filter-panel-container hidden overflow-visible">
            <x-search.filterpanel />
        </div>
    </div>
</div>

<script>
    searchInput = document.getElementById('search');
    const searchIcon = document.getElementById('search-icon');
    const clearIcon = document.getElementById('clear-icon');
    (function() {
        let searchTimeout;
        let filterTimeout;
        let isFiltered = false;
        
        // Register callback for filter changes from filterpanel
        window.onFilterChange = function(filterValues) {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                isFiltered = Object.values(filterValues).some(v => v !== null && v !== undefined && v !== '' && !(Array.isArray(v) && v.length === 0));
                if (isFiltered || searchInput.value) {
                    searchIcon.classList.add('hidden');
                    clearIcon.classList.remove('hidden');
                } 
                if (!isFiltered && !searchInput.value) {
                    searchIcon.classList.remove('hidden');
                    clearIcon.classList.add('hidden');
                }
                updateUrlParams({ page: 1 });
                fetchScans();
            }, 300);
        };
        
        function updateSearchbarIcons(searchInput, searchIcon, clearIcon) {
            if (searchInput.value) {
                searchIcon.classList.add('hidden');
                clearIcon.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIcon.classList.add('hidden');
            }
        }

        function syncSearchbarFromUrl() {

            if (!searchInput || !searchIcon || !clearIcon) {
                return; // Elements not found, exit early
            }

            const params = new URLSearchParams(window.location.search);
            const searchValue = params.get('search') || '';
            searchInput.value = searchValue;
            const hasFilters = ['status', 'reading_status', 'genre_ids'].some(function(key) {
                return params.get(key) || params.getAll(key + '[]').some(Boolean);
            });
            isFiltered = hasFilters;
            if (searchValue || hasFilters) {
                searchIcon.classList.add('hidden');
                clearIcon.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIcon.classList.add('hidden');
            }
        }

        function initSearchbar() {
            if (!searchInput || !searchIcon || !clearIcon) {
                return; // Elements not found, exit early
            }

            // Initialize icons based on current input value (set by server)
            updateSearchbarIcons(searchInput, searchIcon, clearIcon);
            
            // Sync searchbar from URL on load (in case URL params differ from server value)
            syncSearchbarFromUrl();
        
            searchInput.addEventListener('input', function() {
                updateSearchbarIcons(this, searchIcon, clearIcon);

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Use the updateUrlParams function from the parent page if available
                    updateUrlParams({ page: 1, search: this.value });
                    fetchScans();
                }, 500);
            });

            clearIcon.addEventListener('click', function() {
                // Clear search input
                searchInput.value = '';
                isFiltered = false;
                
                // Clear all filters
                if (typeof window.clearAllFilters === 'function') {
                    window.clearAllFilters();
                }
                
                updateSearchbarIcons(searchInput, searchIcon, clearIcon);
                
                // Clear all URL params and reset to base path
                window.history.pushState({}, '', window.location.pathname);
                
                // Update URL params to remove everything
                updateUrlParams({ page: 1, search: '' });
                
                // Trigger filter change to update the display
                if (typeof window.triggerFilterChange === 'function') {
                    window.triggerFilterChange();
                } else {
                    // Fallback: directly call fetchScans if triggerFilterChange is not available
                    fetchScans();
                }
            });
        }

        function initFilterPanel() {
            const openFilterButton = document.getElementById('open-filter-panel');
            if (openFilterButton) {
                openFilterButton.addEventListener('click', function() {
                    document.querySelector('.filter-panel-container').classList.toggle('hidden');
                    document.getElementById('filter-icon').classList.toggle('hidden');
                    document.getElementById('close-filter-icon').classList.toggle('hidden');
                });
            }

            // Hide filter panel when clicking outside
            document.addEventListener('click', function(event) {
                const searchbar = document.getElementById('searchbar');
                const filterPanel = document.querySelector('.filter-panel-container');
                
                if (!searchbar || !filterPanel) return;
                
                // Check if click is outside searchbar and filter panel
                if (!searchbar.contains(event.target) && !filterPanel.contains(event.target)) {
                    // Hide the filter panel
                    filterPanel.classList.add('hidden');
                    // Reset filter icons
                    const filterIcon = document.getElementById('filter-icon');
                    const closeFilterIcon = document.getElementById('close-filter-icon');
                    if (filterIcon) filterIcon.classList.remove('hidden');
                    if (closeFilterIcon) closeFilterIcon.classList.add('hidden');
                }
            });
        }

        function initBrowserNavigation() {
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                syncSearchbarFromUrl();
            });
        }

        // Initialize when DOM is ready
        function initializeAll() {
            initSearchbar();
            initFilterPanel();
            initBrowserNavigation();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAll);
        } else {
            // DOM is already ready, but wait a tick to ensure all scripts are loaded
            setTimeout(initializeAll, 0);
        }

        // Also sync on window load to catch cases where page loads with URL params
        window.addEventListener('load', function() {
            syncSearchbarFromUrl();
        });
    })();
</script>