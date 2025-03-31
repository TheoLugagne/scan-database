@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-300 leading-5">
                    Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- First Page --}}
                    @if($paginator->currentPage() > 1)
                        <a href="{{ $paginator->url(1) }}" 
                            class="relative inline-flex items-center justify-center w-12 px-2 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-l-md leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150"
                            aria-label="{{ __('Go to first page') }}">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    @else
                        <span class="relative inline-flex items-center justify-center w-12 px-2 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-l-md leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150">
                            <span aria-hidden="true">&laquo;</span>
                        </span>
                    @endif

                    {{-- Previous Page --}}
                    @if($paginator->currentPage() > 1)
                        <a href="{{ $paginator->previousPageUrl() }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150"
                        aria-label="{{ __('Go to previous page') }}">
                            <span aria-hidden="true">&lsaquo;</span>
                        </a>
                    @else
                        <span class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150">
                            <span aria-hidden="true">&lsaquo;</span>
                        </span>
                    @endif

                    {{-- Current Page - 1 --}}
                    @if($paginator->currentPage() > 1)
                        <a href="{{ $paginator->url($paginator->currentPage() - 1) }}" 
                            class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150">
                            {{ $paginator->currentPage() - 1 }}
                        </a>
                    @endif

                    {{-- Current Page --}}
                    <span aria-current="page" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-white bg-indigo-600 border border-gray-600 cursor-default leading-5">
                        {{ $paginator->currentPage() }}
                    </span>

                    {{-- Current Page + 1 --}}
                    @if($paginator->currentPage() < $paginator->lastPage())
                        <a href="{{ $paginator->url($paginator->currentPage() + 1) }}" 
                            class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150">
                            {{ $paginator->currentPage() + 1 }}
                        </a>
                    @endif

                    {{-- Next Page --}}
                    @if($paginator->currentPage() < $paginator->lastPage())
                        <a href="{{ $paginator->nextPageUrl() }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150"
                        aria-label="{{ __('Go to next page') }}">
                            <span aria-hidden="true">&rsaquo;</span>
                        </a>
                    @else
                        <span class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150">
                            <span aria-hidden="true">&rsaquo;</span>
                        </span>
                    @endif

                    {{-- Last Page --}}
                    @if($paginator->currentPage() < $paginator->lastPage())
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" 
                        class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-r-md leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150"
                        aria-label="{{ __('Go to last page') }}">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    @else
                        <span class="relative inline-flex items-center justify-center w-12 px-2 py-2 -ml-px text-sm font-medium text-gray-300 bg-gray-800 border border-gray-600 rounded-r-md leading-5 hover:text-white focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-700 active:text-white transition ease-in-out duration-150">
                            <span aria-hidden="true">&raquo;</span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif 