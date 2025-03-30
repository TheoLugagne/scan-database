@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Scan Details</h1>
            <a href="{{ route('scan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                Back to List
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm">Title</h3>
                    <p class="text-white text-lg">{{ $scan->title }}</p>
                </div>

                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm">Current Chapter</h3>
                    <div class="flex items-center space-x-2">
                        <input type="number" 
                            id="current_chapter" 
                            value="{{ $scan->current_chapter }}" 
                            step="0.1"
                            class="mt-1 block w-32 rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        
                        <button type="button"
                            onclick="incrementChapter()"
                            class="mt-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded flex items-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>

                        <button type="button"
                            onclick="updateChapter()"
                            class="mt-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded">
                            Validate
                        </button>
                    </div>
                    <div id="chapter-update-message" class="mt-2 text-sm hidden"></div>
                </div>

                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm">Last Update</h3>
                    <p class="text-white text-lg">{{ $scan->last_update ? date('F j, Y', strtotime($scan->last_update)) : 'N/A' }}</p>
                </div>

                @if($scan->link_to_scan)
                <div class="border-b border-gray-700 pb-4">
                    <h3 class="text-gray-400 text-sm">Scan Link</h3>
                    <a href="{{ $scan->link_to_scan }}" class="text-blue-400 hover:text-blue-300" target="_blank">
                        View Original Scan
                    </a>
                </div>
                @endif
            </div>

            <div class="bg-gray-900 rounded-lg p-6">
                @if($scan->cover_image)
                    <h3 class="text-white text-xl mb-4">Cover Image</h3>
                    <img src="{{ asset('storage/' . $scan->cover_image) }}"
                    alt="{{ $scan->title }}" class="max-w-full h-56 object-contain rounded">
                @else
                    <p class="text-gray-400">No cover image available</p>
                @endif
            </div>
        </div>

        <div class="mt-8 bg-gray-900 rounded-lg p-6">
            <h3 class="text-white text-xl mb-4">Summary</h3>
            <p class="text-gray-300">{{ $scan->summary ?? 'No summary available' }}</p>
        </div>

        <div class="mt-6 flex space-x-4">
            <a href="{{ route('scan.edit', $scan) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Edit Scan
            </a>
            <button 
                onclick="showDeleteModal('{{ $scan->id }}', '{{ $scan->title }}')"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded cursor-pointer">
                Delete Scan
            </button>
        </div>
    </div>
</div>

<x-delete-modal />

<script>
function incrementChapter() {
    const input = document.getElementById('current_chapter');
    input.value = (parseFloat(input.value) + 1).toFixed(1);
}

function updateChapter() {
    const chapter = document.getElementById('current_chapter').value;
    const messageDiv = document.getElementById('chapter-update-message');
    
    // Create form data
    const formData = new FormData();
    formData.append('current_chapter', chapter);
    formData.append('_token', '{{ csrf_token() }}');

    // Send request
    fetch('/scan/{{ $scan->id }}/update-chapter', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.classList.remove('hidden');
        if (data.success) {
            messageDiv.className = 'mt-2 text-sm text-green-500';
            messageDiv.textContent = 'Chapter updated successfully';
        } else {
            messageDiv.className = 'mt-2 text-sm text-red-500';
            messageDiv.textContent = 'Error updating chapter';
        }
        
        // Hide message after 3 seconds
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 3000);
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mt-2 text-sm text-red-500';
        messageDiv.textContent = 'Error updating chapter';
        
        // Hide message after 3 seconds
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 3000);
    });
}
</script>
@endsection
