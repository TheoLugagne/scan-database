@props(["type" => ['UserScanProgress', 'Scan']])

@php
use App\Models\Scan;
use App\Models\UserScanProgress;

$type_list = [
    'UserScanProgress' => UserScanProgress::class,
    'Scan' => Scan::class,
];

foreach ($type as $t) {
    if (!array_key_exists($t, $type_list)) {
        throw new Exception("Invalid type: " . $t);
    }
}

$filter_sections_data = [];
foreach ($type as $t) {
    $filter_sections_data = array_merge($filter_sections_data, $type_list[$t]::getFilterSectionsData());
}

@endphp
<div class="filter-panel absolute left-1/2 top-full z-50 -translate-x-1/2 mt-2 w-[42rem] max-w-[calc(100vw-2rem)] p-4 text-base bg-gray-800 border border-gray-600 rounded-lg shadow-lg overflow-visible grid grid-cols-3 gap-2 [&_.selector-display]:text-sm">
    @foreach ($filter_sections_data as $loopIndex => $nameAndData)
        @php
            [$name, $filter_section_data] = [$loopIndex, $nameAndData];
            // Use the 'name' field from the data, fallback to array key if not set
            $fieldName = $filter_section_data['name'] ?? $name;
        @endphp
        <x-search.filtersection :title="$filter_section_data['title']" :elts="$filter_section_data['elts']" :type="$filter_section_data['type']" :name="$fieldName" />
    @endforeach
</div>

<script>
(function() {
    let filterPanel = null;
    
    function findFilterPanel() {
        return document.querySelector('.filter-panel-container .filter-panel');
    }
    
    // Function to collect all filter values from all filtersection components
    function collectAllFilterValues() {
        if (!filterPanel) {
            filterPanel = findFilterPanel();
        }
        
        if (!filterPanel) {
            return {};
        }
        
        const filterValues = {};
        
        // Collect values from all filter-section-value hidden inputs
        const filterSectionInputs = filterPanel.querySelectorAll('input[id^="filter-section-value-"]');
        
        filterSectionInputs.forEach(input => {
            const fieldName = input.id.replace('filter-section-value-', '');
            const inputValue = input.value || '';
            
            // Always include the field, even if empty, so we can explicitly remove it from URL params
            if (inputValue && inputValue.trim()) {
                // Handle comma-separated values (multiselect)
                if (inputValue.includes(',')) {
                    const values = inputValue.split(',').filter(v => v.trim());
                    // Only set if there are actual values
                    if (values.length > 0) {
                        filterValues[fieldName] = values;
                    } else {
                        filterValues[fieldName] = null;
                    }
                } else {
                    filterValues[fieldName] = inputValue;
                }
            } else {
                // Explicitly set to null when empty so it can be removed from URL
                filterValues[fieldName] = null;
            }
        });
        
        return filterValues;
    }
    
    // Expose function globally
    window.collectAllFilterValues = collectAllFilterValues;

    // Paint selectors from the URL without emitting change (callers fetch afterwards).
    window.applyFiltersFromUrl = function(params) {
        const sectionInputs = document.querySelectorAll('input[id^="filter-section-value-"]');
        sectionInputs.forEach(sectionInput => {
            const name = sectionInput.id.replace('filter-section-value-', '');
            let value = params.get(name) || '';
            if (!value) {
                const many = params.getAll(name + '[]').filter(Boolean);
                if (many.length) {
                    value = many.join(',');
                }
            }
            if (window.filterSetters && typeof window.filterSetters[name] === 'function') {
                window.filterSetters[name](value);
            }
            sectionInput.value = value;
        });
    };

    // Function to clear all filter values
    function clearAllFilters() {
        if (!filterPanel) {
            filterPanel = findFilterPanel();
        }

        if (!filterPanel) {
            return;
        }

        filterPanel.querySelectorAll('input[id^="filter-section-value-"]').forEach(input => {
            const name = input.id.replace('filter-section-value-', '');
            if (window.filterSetters && typeof window.filterSetters[name] === 'function') {
                window.filterSetters[name]('');
            }
            input.value = '';
        });
    }
    
    // Expose clearAllFilters globally
    window.clearAllFilters = clearAllFilters;
    
    // Function to trigger filter change callback
    function triggerFilterChange() {
        if (typeof window.onFilterChange === 'function') {
            const filterValues = collectAllFilterValues();
            window.onFilterChange(filterValues);
        }
    }
    
    // Expose triggerFilterChange globally so filtersection can call it directly
    window.triggerFilterChange = triggerFilterChange;
    
    // Set up listeners for filter changes
    function setupFilterChangeListeners() {
        filterPanel = findFilterPanel();
        
        if (!filterPanel) {
            // Retry if filter panel hasn't loaded yet
            setTimeout(setupFilterChangeListeners, 100);
            return;
        }
        
        // Listen for changes on filter-section-value hidden inputs
        const filterSectionInputs = filterPanel.querySelectorAll('input[id^="filter-section-value-"]');
        filterSectionInputs.forEach(input => {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        triggerFilterChange();
                    }
                });
            });
            observer.observe(input, { attributes: true, attributeFilter: ['value'] });
        });
        
        // Watch for new inputs being added dynamically
        const panelObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        // Check if a new hidden input was added
                        if (node.tagName === 'INPUT' && node.type === 'hidden') {
                            const observer = new MutationObserver(function(mutations) {
                                mutations.forEach(function(mutation) {
                                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                                        triggerFilterChange();
                                    }
                                });
                            });
                            observer.observe(node, { attributes: true, attributeFilter: ['value'] });
                        }
                        // Check if new inputs were added within the node
                        const newInputs = node.querySelectorAll && node.querySelectorAll('input[type="hidden"]');
                        if (newInputs) {
                            newInputs.forEach(input => {
                                const observer = new MutationObserver(function(mutations) {
                                    mutations.forEach(function(mutation) {
                                        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                                            triggerFilterChange();
                                        }
                                    });
                                });
                                observer.observe(input, { attributes: true, attributeFilter: ['value'] });
                            });
                        }
                    }
                });
            });
        });
        
        panelObserver.observe(filterPanel, { childList: true, subtree: true });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupFilterChangeListeners);
    } else {
        setupFilterChangeListeners();
    }
})();
</script>