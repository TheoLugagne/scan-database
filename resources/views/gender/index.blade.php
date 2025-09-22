<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            {{-- Left side with responsive width --}}
            <div class="w-24 sm:w-48">
                <h2 class="font-semibold text-xl text-white whitespace-nowrap">
                    {{ __('Genders') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl py-8 bg-gray-900 rounded-lg shadow-xl mx-auto sm:px-6 lg:px-8">
        {{-- Add Gender Button --}}
        <div class="mb-6">
            <a href="{{ route('gender.create') }}" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Gender
            </a>
        </div>

        {{-- Genders List --}}
        <div id="genders-container">
            @if($genders->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-400">No genders found.</p>
                </div>
            @else
                <div class="bg-gray-800 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Created
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Updated
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach($genders as $gender)
                                <tr class="hover:bg-gray-750 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">
                                            {{ $gender->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $gender->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $gender->updated_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('gender.edit', $gender) }}"
                                                class="text-gray-400 hover:text-white"
                                                title="Edit Gender">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            @if(auth()->check() && auth()->user()->role === 'admin')
                                            <button type="button"
                                                onclick="showDeleteGenderModal('{{ $gender->id }}', '{{ $gender->name }}')"
                                                class="text-gray-400 hover:text-white cursor-pointer"
                                                title="Delete Gender">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <x-delete-modal />

    @push('scripts')
    <script>
        // Function to show delete modal for genders
        function showDeleteGenderModal(id, name) {
            const modal = document.getElementById('delete-modal');
            const modalTitle = document.getElementById('delete-modal-title');
            const modalMessage = document.getElementById('delete-modal-message');
            const deleteForm = document.getElementById('delete-form');
            
            modalTitle.textContent = 'Delete Gender';
            modalMessage.textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
            deleteForm.action = `/gender/${id}`;
            
            modal.classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-layout>
