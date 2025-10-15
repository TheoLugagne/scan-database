@props(['elts' => [], 'selected_elt' => null, 'title' => 'Items', 'field_name' => 'item_ids'])

<div id="m2o_selector">
    <label class="block text-base font-medium text-gray-300">{{ $title }}</label>
    <div class="relative mt-2">
        <div id="selected_elt" class="block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 cursor-pointer @error('elts') border-red-500 @enderror">
            Select {{ $title }}...
        </div>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        <div id="elt-dropdown" class="hidden absolute z-10 w-full mt-1 bg-gray-800 border border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            @foreach($elts as $elt)
                <div class="elt-option px-4 py-2 text-white hover:bg-gray-700 cursor-pointer flex items-center" data-value="{{ $elt }}" data-name="{{ $elt->label() }}">
                    <span class="mr-3 rounded border-gray-600 focus:ring-indigo-500" value="{{ $elt }}">{{ $elt->label() }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @error('elts')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
    <p class="mt-1 text-sm text-gray-400">Click to select a {{ $title }}</p>
    <input type="hidden" id="status" name="{{ $field_name }}" value="{{ $selected_elt }}"/> 
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldName = document.getElementById('field_name').value;
    const title = '{{ $title }}';
    const elt_display = document.getElementById('selected_elt');
    const eltDropdown = document.getElementById('elt-dropdown');
    const eltOptions = document.querySelectorAll('.elt-option');
    const statusInput = document.getElementById('status');
    
    // Initialize selected element
    let selectedElt = @json($selected_elt);
    
    // Set initial display if there's a selected element
    if (selectedElt) {
        elt_display.textContent = selectedElt.label || selectedElt;
        statusInput.value = selectedElt;
    }
    updateInputDisplay();
    // Toggle dropdown
    elt_display.addEventListener('click', function() {
        eltDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!elt_display.contains(e.target) && !eltDropdown.contains(e.target)) {
            eltDropdown.classList.add('hidden');
        }
    });

    // Handle element selection
    eltOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            const value = this.dataset.value;
            const name = this.dataset.name;

            // Update selected element
            selectedElt = value;
            updateInputDisplay();
            
            // Close dropdown
            eltDropdown.classList.add('hidden');
        });
    });

    function updateInputDisplay() {
        if (!selectedElt) {
            elt_display.textContent = 'Select ' + title + '...';
            statusInput.value = '';
        } else {
            // Find the name from the selected element
            const selectedOption = Array.from(eltOptions).find(option => option.dataset.value === selectedElt);
            const displayName = selectedOption ? selectedOption.dataset.name : selectedElt;
            
            // Simple text display instead of complex HTML
            elt_display.textContent = displayName;
            statusInput.value = selectedElt;
        }
    }
});
</script>