@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex-1 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center">
                <p class="text-sm text-gray-300 leading-5">
                    Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                </p>
            </div>

            <div class="flex justify-center">
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- First Page --}}
                    <a data-page-url="{{ route('scans.fetch', ['page' => 1, 'per_page' => (int)request('per_page', 12)]) }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-l-md leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150 cursor-pointer"
                        aria-label="{{ __('Go to first page') }}">
                        <span aria-hidden="true">&laquo;</span>
                    </a>

                    {{-- Previous Page --}}
                    <a data-page-url="{{ route('scans.fetch', ['page' => $paginator->currentPage() - 1, 'per_page' => (int)request('per_page', 12)]) }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150 cursor-pointer"
                        aria-label="{{ __('Go to previous page') }}">
                        <span aria-hidden="true">&lsaquo;</span>
                    </a>

                    {{-- Current Page - 1 --}}
                    @if($paginator->currentPage() > 1)
                        <a data-page-url="{{ route('scans.fetch', ['page' => $paginator->currentPage() - 1, 'per_page' => (int)request('per_page', 12)]) }}" 
                            class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150 cursor-pointer">
                            {{ $paginator->currentPage() - 1 }}
                        </a>
                    @endif

                    {{-- Current Page --}}
                    <input type="number" 
                       id="goto-page"
                       min="1" 
                       max="{{ $paginator->lastPage() }}"
                       value="{{ $paginator->currentPage() }}"
                       class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-white bg-indigo-600 border border-gray-600 cursor-default leading-5 text-center [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                       onchange="gotoPage(this.value)">

                    {{-- Current Page + 1 --}}
                    @if($paginator->currentPage() < $paginator->lastPage())
                        <a data-page-url="{{ route('scans.fetch', ['page' => $paginator->currentPage() + 1, 'per_page' => (int)request('per_page', 12)]) }}" 
                            class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150 cursor-pointer">
                            {{ $paginator->currentPage() + 1 }}
                        </a>
                    @endif

                    {{-- Next Page --}}
                    <a data-page-url="{{ route('scans.fetch', ['page' => min($paginator->lastPage(), $paginator->currentPage() + 1), 'per_page' => (int)request('per_page', 12)]) }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150 cursor-pointer"
                        aria-label="{{ __('Go to next page') }}">
                        <span aria-hidden="true">&rsaquo;</span>
                    </a>

                    {{-- Last Page --}}
                    <a data-page-url="{{ route('scans.fetch', ['page' => $paginator->lastPage(), 'per_page' => (int)request('per_page', 12)]) }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-r-md leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150 cursor-pointer"
                        aria-label="{{ __('Go to last page') }}">
                        <span aria-hidden="true">&raquo;</span>
                    </a>

                    {{-- Page Input --}}
                    
                    </span>
            </div>
        </div>
    </nav>

    <script>
        function handlePaginationClick(event) {
            const link = event.target.closest('[data-page-url]');
            if (link) {
                event.preventDefault();
                
                // Get current URL parameters
                const currentParams = new URLSearchParams(window.location.search);
                // Get the new URL from the pagination link
                const newUrl = new URL(link.dataset.pageUrl);
                const newParams = new URLSearchParams(newUrl.search);
                
                // Preserve all existing parameters except page and per_page
                for (const [key, value] of currentParams.entries()) {
                    if (key !== 'page' && key !== 'per_page') {
                        newParams.set(key, value);
                    }
                }
                
                // Update the URL with all parameters
                fetchScans(`${newUrl.pathname}?${newParams.toString()}`);
            }
        }

        function gotoPage(value) {
            value = Math.min(Math.max(1, parseInt(value) || 1), {{ $paginator->lastPage() }});
            const currentPerPage = document.getElementById('per_page').value || {{ request('per_page', 12) }};
            
            const params = new URLSearchParams(window.location.search);
            params.set('page', value);
            params.set('per_page', currentPerPage);
            
            fetchScans('{{ route('scans.fetch') }}?' + params.toString());
        }
    </script>
@endif 