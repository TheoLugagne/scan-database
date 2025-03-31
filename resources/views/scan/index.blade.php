<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white">
                {{ __('My Scans') }}
            </h2>
            
            <div class="flex items-center space-x-2">
                <label for="per_page" class="text-sm text-gray-300">Items per page:</label>
                <input type="number" 
                       id="per_page" 
                       value="{{ request('per_page', 12) }}"
                       min="1"
                       class="bg-gray-800 border border-gray-600 text-gray-300 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-16 p-2.5 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
            </div>
        </div>
    </x-slot>

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
            const perPage = document.getElementById('per_page').value || 12;
            const params = new URLSearchParams(window.location.search);
            
            params.set('per_page', perPage);
            
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

        // Initial event listeners setup
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-page-url]').forEach(button => {
                button.addEventListener('click', handlePaginationClick);
            });
        });

        function updateItemsPerPage(value) {
            value = Math.max(1, parseInt(value) || 12);
            document.getElementById('per_page').value = value;
            document.getElementById('per_page').blur();
            
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1'); // Reset to first page
            fetchScans('{{ route('scans.fetch') }}?' + params.toString());
        }

        // Handle both change and Enter key
        const perPageInput = document.getElementById('per_page');
        
        perPageInput.addEventListener('change', function() {
            updateItemsPerPage(this.value);
        });

        perPageInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                updateItemsPerPage(this.value);
            }
        });
    </script>
    @endpush
</x-app-layout> 
