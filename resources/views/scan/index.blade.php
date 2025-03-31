<x-app-layout>
    <!-- <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white">
                {{ __('My Scans') }}
            </h2>
            
            <div class="flex items-center space-x-2">
                <label for="per_page" class="text-sm text-gray-300">Items per page:</label>
                <input type="number" 
                       id="per_page" 
                       value="{{ $perPage }}"
                       min="1"
                       class="bg-gray-800 border border-gray-600 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-16 p-2.5 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
            </div>
        </div>
    </x-slot> -->

    <div class="bg-gray-900 rounded-lg shadow-xl p-8">
        <div id="scans-container">
            @include('scan.partials.scan-list')
        </div>
    </div>

    <!-- Replace the existing modal with the component -->
    <x-delete-modal />

    @push('scripts')
    <script>
        function fetchScans(url = null) {
            url = url || '{{ route('scans.fetch') }}';
            const params = new URLSearchParams(window.location.search);
            
            // Add loading state
            document.getElementById('scans-container').classList.add('opacity-50');
            
            fetch(`${url}?${params.toString()}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('scans-container').innerHTML = html;
                    // Update URL without page reload
                    window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
                })
                .finally(() => {
                    document.getElementById('scans-container').classList.remove('opacity-50');
                });
        }

        // Handle per-page changes
        document.getElementById('per_page').addEventListener('change', function() {
            let value = Math.max(1, parseInt(this.value) || 12);
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1'); // Reset to first page
            fetchScans(`{{ route('scans.fetch') }}?${params.toString()}`);
        });

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const element = e.target.closest('[data-page-url]');
            if (element) {
                e.preventDefault();
                fetchScans(element.dataset.pageUrl);
            }
        });
    </script>
    @endpush
</x-app-layout> 
