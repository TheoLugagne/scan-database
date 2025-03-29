@extends('layouts.app')

@section('content')
<div class="bg-gray-900 rounded-lg shadow-xl p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-white">My Scans</h2>
            <a href="{{ route('scan.create') }}" 
                class="px-4 py-2 text-base font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500">
                Add New Scan
            </a>
        </div>

        @if($scans->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-300">No scans</h3>
                <p class="mt-1 text-sm text-gray-400">Get started by creating a new scan.</p>
                <div class="mt-6">
                    <a href="{{ route('scan.create') }}" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        New Scan
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($scans as $scan)
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        @if($scan->cover_image)
                            <div class="relative w-full h-64">
                                <img src="{{ asset('storage/' . $scan->cover_image) }}" 
                                    alt="{{ $scan->title }}"
                                    class="absolute inset-0 w-full h-full object-contain bg-gray-700">
                            </div>
                        @endif
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-white mb-2">{{ $scan->title }}</h3>
                            @if($scan->summary)
                                <p class="text-gray-400 text-sm mb-4 line-clamp-3">{{ $scan->summary }}</p>
                            @endif
                            <div class="flex items-center justify-between text-sm text-gray-400">
                                <span>Chapter {{ $scan->current_chapter }}</span>
                                <span>{{ $scan->create_date->format('M d, Y') }}</span>
                            </div>
                            @if($scan->link_to_scan)
                                <div class="mt-4">
                                    <a href="{{ $scan->link_to_scan }}" 
                                        target="_blank"
                                        class="inline-flex items-center text-sm text-indigo-400 hover:text-indigo-300">
                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        View Scan
                                    </a>
                                </div>
                            @endif
                            <div class="mt-4 flex justify-end space-x-2">
                                <a href="{{ route('scan.edit', $scan) }}" 
                                    class="text-gray-400 hover:text-white">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" 
                                    onclick="showDeleteModal('{{ $scan->id }}', '{{ $scan->title }}')"
                                    class="text-gray-400 hover:text-red-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden overflow-y-auto h-full w-full transition-opacity duration-300 ease-in-out">
    <div class="relative top-20 mx-auto p-5 w-96 shadow-2xl rounded-lg bg-gray-800 transform transition-all duration-300 ease-in-out scale-0 opacity-0 border border-gray-700">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-900 bg-opacity-20">
                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-white mt-2">Delete Scan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-400">
                    Are you sure you want to delete <span id="deleteScanTitle" class="font-semibold text-white"></span>?
                    This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="hideDeleteModal()" 
                    class="px-4 py-2 bg-gray-700 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showDeleteModal(scanId, scanTitle) {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div');
    const form = document.getElementById('deleteForm');
    const titleSpan = document.getElementById('deleteScanTitle');
    
    // Set the scan title in the modal
    titleSpan.textContent = scanTitle;
    
    // Update the form action
    form.action = `/scan/${scanId}`;
    
    // Show the modal with animation
    modal.classList.remove('hidden');
    // Trigger reflow
    modal.offsetHeight;
    modal.classList.add('bg-opacity-50');
    modalContent.classList.remove('scale-0', 'opacity-0');
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div');
    
    // Hide with animation
    modalContent.classList.add('scale-0', 'opacity-0');
    modal.classList.remove('bg-opacity-50');
    
    // Wait for animation to complete before hiding
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>
@endsection 