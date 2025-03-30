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
                    <p class="text-white text-lg">{{ $scan->current_chapter }}</p>
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
                    <img src="{{ $scan->cover_image }}" alt="Cover Image" class="max-w-full h-auto rounded">
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
@endsection


