@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 rounded-lg shadow-xl p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-white">{{ $scan->title }}</h2>
                <a href="{{ route('scan.index') }}"
                   class="px-4 py-2 text-base font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500">
                    Back to Scans
                </a>
            </div>

            @if($scan->cover_image)
                <div class="relative w-full h-64 mb-8">
                    <img src="{{ asset('storage/' . $scan->cover_image) }}"
                         alt="{{ $scan->title }}"
                         class="absolute inset-0 w-full h-full object-contain bg-gray-700">
                </div>
            @endif

            <div class="text-gray-300">
                <p class="mb-4"><strong>Summary:</strong> {{ $scan->summary ?? 'No summary available' }}</p>
                <div class="mb-4 flex items-center">
                    <strong class="mr-2">Current Chapter:</strong>
                    <form action="{{ route('scan.update', $scan) }}" method="POST" class="flex items-center">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="current_chapter" value="{{ $scan->current_chapter }}" class="w-16 text-gray-900 rounded-md">
                        <button type="submit" class="ml-2 px-2 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Update
                        </button>
                    </form>
                    <form action="{{ route('scan.update', $scan) }}" method="POST" class="ml-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="current_chapter" value="{{ $scan->current_chapter + 1 }}">
                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            +
                        </button>
                    </form>
                </div>
                <p class="mb-4"><strong>Created on:</strong> {{ $scan->create_date->format('M d, Y') }}</p>
                @if($scan->link_to_scan)
                    <p class="mb-4">
                        <strong>Link to Scan:</strong>
                        <a href="{{ $scan->link_to_scan }}" target="_blank" class="text-indigo-400 hover:text-indigo-300">
                            View Scan
                        </a>
                    </p>
                @endif
            </div>

            <div class="flex justify-end space-x-4 mt-8">
{{--                <a href="{{ route('scan.edit', $scan) }}"--}}
{{--                   class="px-4 py-2 text-base font-medium text-gray-300 hover:text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500">--}}
{{--                    Edit--}}
{{--                </a>--}}
                <button type="button"
                        onclick="showDeleteModal('{{ $scan->id }}', '{{ $scan->title }}')"
                        class="px-4 py-2 text-base font-medium text-gray-300 hover:text-red-400 bg-gray-800 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-red-500">
                    Delete
                </button>
            </div>
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
