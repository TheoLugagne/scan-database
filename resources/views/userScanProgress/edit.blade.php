<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            {{ __('Edit') }} - {{ $scan->title }}
        </h2>
    </x-slot>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('scan.update', $scan) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Info Message (No Changes) --}}
            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('info') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Error Message --}}
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            @endif

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
                        <input type="number" step="0.5" name="current_chapter" id="current_chapter" 
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
                        <textarea name="summary" id="summary" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">{{ old('summary', $scan->summary) }}</textarea>
                        @error('summary')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex space-x-4">
                        <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                            Update Scan
                        </button>
                        <a href="{{ session('scan_previous_url', route('scan.index')) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back
                        </a>
                    </div>
                </div>

                <div class="bg-gray-900 rounded-lg p-6">
                    <h3 class="text-white text-xl mb-4">Cover Image</h3>
                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-300">Cover Image</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" 
                                   name="cover_image" 
                                   id="cover_image" 
                                   accept="image/*"
                                   value="{{ old('cover_image', $scan->cover_image) }}"
                                   class="block w-full text-sm text-gray-300
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-indigo-600 file:text-white
                                          hover:file:bg-indigo-700
                                          file:cursor-pointer
                                          border border-gray-600 rounded-md
                                          bg-gray-800 
                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('cover_image')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    @if($scan->cover_image)
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . $scan->cover_image) }}" 
                                alt="Current cover" 
                                class="max-w-full h-48 object-contain rounded">
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Optional: Add JavaScript for auto-dismissing messages --}}
    <script>
        // Auto-dismiss messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => alert.remove());
        }, 5000);
    </script>
</x-app-layout>
