<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            {{ __('Add to your scans') }} - {{ $scan->title }}
        </h2>
    </x-slot>

    <div class="bg-gray-800 rounded-lg shadow-lg p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-900 border border-red-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-200">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-200">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <form method="POST" action="{{ route('userScanProgress.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="scan_id" value="{{ $scan->id }}">
                <div class="space-y-4">
                    <!-- Title -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Title</h3>
                        <input readonly type="text" name="title" id="title" 
                            value="{{ $scan->title }}"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Chapter -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Current Chapter</h3>
                        <input type="number" step="0.5" name="current_chapter" id="current_chapter" 
                            value="{{ 0 }}"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        @error('current_chapter')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Link to Scan -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Link to Scan</h3>
                        <input readonly type="url" name="link_to_scan" id="link_to_scan" 
                            value="{{ $scan->link_to_scan }}"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">
                        @error('link_to_scan')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Summary -->
                    <div class="border-b border-gray-700 pb-4">
                        <h3 class="text-gray-400 text-sm">Summary</h3>
                        <textarea readonly name="summary" id="summary" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-base">{{ $scan->summary }}</textarea>
                        @error('summary')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex space-x-4">
                        <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                            Add Scan
                        </button>
                        <a href="{{ session('scan_previous_url', route('scan.index')) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back
                        </a>
                    </div>
                </div>

                <div class="bg-gray-900 rounded-lg p-6">
                    <h3 class="text-white text-xl mb-4">Cover Image</h3>
                    @if($scan->cover_image)
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . $scan->cover_image) }}" 
                                alt="Current cover" 
                                class="max-w-full h-96 object-contain rounded">
                        </div>
                    @else
                        <p class="text-gray-400">No cover image available</p>
                    @endif
                </>
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
