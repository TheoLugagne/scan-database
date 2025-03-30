<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            {{ __('Create New Scan') }}
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
        
        <form action="{{ route('scan.store') }}" method="POST" class="space-y-8" enctype="multipart/form-data" novalidate>
            @csrf
            
            <div>
                <label for="title" class="block text-base font-medium text-gray-300">Title</label>
                <input type="text" name="title" id="title" required
                    minlength="1" maxlength="255"
                    value="{{ old('title') }}"
                    class="mt-2 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-400">Required, must be unique</p>
            </div>

            <div>
                <label for="summary" class="block text-base font-medium text-gray-300">Summary</label>
                <textarea name="summary" id="summary" rows="10"
                    class="mt-2 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 @error('summary') border-red-500 @enderror">{{ old('summary') }}</textarea>
                @error('summary')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-400">Optional</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="current_chapter" class="block text-base font-medium text-gray-300">Current Chapter</label>
                    <input type="number" name="current_chapter" id="current_chapter" required
                        min="0" step="0.5"
                        value="0"
                        class="mt-2 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 @error('current_chapter') border-red-500 @enderror">
                    @error('current_chapter')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-400">Required, must be 0 or greater</p>
                </div>

                <div>
                    <label for="cover_image" class="block text-base font-medium text-gray-300">Cover Image</label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        class="mt-2 block w-full text-base text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-base file:font-medium file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 @error('cover_image') border-red-500 @enderror">
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-400">Optional, max 2MB, JPG/PNG/GIF/WEBP only</p>
                </div>
            </div>

            <div>
                <label for="link_to_scan" class="block text-base font-medium text-gray-300">Link to Scan</label>
                <input type="url" name="link_to_scan" id="link_to_scan"
                    placeholder="https://example.com"
                    value="{{ old('link_to_scan') }}"
                    class="mt-2 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 @error('link_to_scan') border-red-500 @enderror">
                @error('link_to_scan')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-400">Optional, must be a valid URL</p>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('scan.index') }}" 
                    class="px-6 py-2.5 text-base font-medium text-gray-300 hover:text-white bg-gray-800 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-base font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-indigo-500">
                    Add Scan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script> 