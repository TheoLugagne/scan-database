<div id="createModal" class="fixed inset-0 hidden overflow-y-auto h-full w-full transition-opacity duration-300 ease-in-out">
    <div class="relative top-20 mx-auto p-5 w-96 shadow-2xl rounded-lg bg-gray-800 transform transition-all duration-300 ease-in-out scale-0 opacity-0 border border-gray-700">
        <div class="mt-3 text-center">
            <form id="createForm" method="POST" class="inline">
                <h3 class="text-lg leading-6 font-medium text-white mt-2">Create Genre</h3>
                <div class="mt-2 px-7 py-3">
                    
                        <label for="name" class="block text-base font-medium text-gray-300">Name</label>
                        @csrf
                        @method('POST')
                        <input type="text" name="name" id="name" class="mt-2 block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2">
                </div>
                <div class="items-center px-4 py-1">
                    <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Create
                    </button>
                </div>
                <div class="items-center px-4 py-1">
                    <button onclick="hideCreateModal()" 
                        class="px-4 py-2 bg-gray-700 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCreateGenreModal(genreName) {
    const modal = document.getElementById('createModal');
    const modalContent = modal.querySelector('div');
    const form = document.getElementById('createForm');
    const nameInput = document.getElementById('name');

    form.action = `{{ route('genre.store') }}`;
    
    modal.classList.remove('hidden');
    modal.offsetHeight;
    modal.classList.add('bg-opacity-50');
    modalContent.classList.remove('scale-0', 'opacity-0');
}

function hideCreateModal() {
    const modal = document.getElementById('createModal');
    const modalContent = modal.querySelector('div');
    
    modalContent.classList.add('scale-0', 'opacity-0');
    modal.classList.remove('bg-opacity-50');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>