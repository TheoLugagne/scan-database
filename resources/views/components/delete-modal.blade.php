<div id="deleteModal" class="fixed inset-0 hidden overflow-y-auto h-full w-full transition-opacity duration-300 ease-in-out">
    <div class="relative top-20 mx-auto p-5 w-96 shadow-2xl rounded-lg bg-gray-800 transform transition-all duration-300 ease-in-out scale-0 opacity-0 border border-gray-700">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-900 bg-opacity-20">
                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-white mt-2">Delete</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-400">
                    Are you sure you want to delete <span id="deleteTitle" class="font-semibold text-white"></span>?
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
function showDeleteScanModal(scanId, scanTitle) {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div');
    const form = document.getElementById('deleteForm');
    const titleSpan = document.getElementById('deleteTitle');
    
    titleSpan.textContent = scanTitle;
    form.action = `/scan/${scanId}`;
    
    modal.classList.remove('hidden');
    modal.offsetHeight;
    modal.classList.add('bg-opacity-50');
    modalContent.classList.remove('scale-0', 'opacity-0');
}

function showDeleteUserScanProgressModal(scanId, scanTitle) {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div');
    const form = document.getElementById('deleteForm');
    const titleSpan = document.getElementById('deleteTitle');
    
    titleSpan.textContent = scanTitle;
    form.action = `/userScanProgress/${scanId}`;
    
    modal.classList.remove('hidden');
    modal.offsetHeight;
    modal.classList.add('bg-opacity-50');
    modalContent.classList.remove('scale-0', 'opacity-0');
}

function showDeleteGenreModal(genreId, genreName) {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div');
    const form = document.getElementById('deleteForm');
    const titleSpan = document.getElementById('deleteTitle');
    
    titleSpan.textContent = genreName;
    form.action = `/genre/${genreId}`;

    modal.classList.remove('hidden');
    modal.offsetHeight;
    modal.classList.add('bg-opacity-50');
    modalContent.classList.remove('scale-0', 'opacity-0');
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('div');
    
    modalContent.classList.add('scale-0', 'opacity-0');
    modal.classList.remove('bg-opacity-50');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script> 