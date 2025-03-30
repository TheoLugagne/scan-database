@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Edit Scan</h1>
            <a href="{{ url()->previous() }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                back
            </a>
        </div>

        <form action="{{ route('scan.update', $scan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <!-- Title -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Title</h3>
                        <input type="text" name="title" id="title" 
                            value="{{ old('title', $scan->title) }}"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Chapter -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Current Chapter</h3>
                        <input type="number" step="0.1" name="current_chapter" id="current_chapter" 
                            value="{{ old('current_chapter', $scan->current_chapter) }}"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        @error('current_chapter')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Link to Scan -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Link to Scan</h3>
                        <input type="url" name="link_to_scan" id="link_to_scan" 
                            value="{{ old('link_to_scan', $scan->link_to_scan) }}"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        @error('link_to_scan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Summary -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Summary</h3>
                        <textarea name="summary" id="summary" rows="6"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">{{ old('summary', $scan->summary) }}</textarea>
                        @error('summary')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-gray-900 rounded-lg p-6">
                    <!-- Cover Image -->
                    <h3 class="text-white text-xl mb-4">Cover Image</h3>
                    <input type="file" name="cover_image" id="cover_image" 
                        class="mt-1 block w-full text-sm text-gray-300
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-medium
                        file:bg-gray-600 file:text-white
                        hover:file:bg-gray-700">
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    
                    @if($scan->cover_image)
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . $scan->cover_image) }}" 
                                alt="Current cover" 
                                class="max-w-full h-100 object-contain rounded">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex space-x-4">
                <button type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                    Update Scan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection