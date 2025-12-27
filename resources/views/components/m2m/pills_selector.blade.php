@props(['elts' => [], 'selected_elts' => [], 'title' => 'Items', 'field_name' => 'item_ids', 'editable' => false, 'no_title' => false, 'no_desc' => false])

<div id="m2m_pills_selector">
    @if (!$no_title)
        <label class="block text-base font-medium text-gray-300">{{ $title }}</label>
    @endif
    <input type="hidden" id="field_name" value="{{ $field_name }}">
    <p class="hidden" id="editable">{{ $editable ? 'true' : 'false' }}</p>
    <div class="relative mt-2">
        <div id="selected_elts" class="block w-full rounded-md bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 cursor-pointer @error('elts') border-red-500 @enderror">
            Select {{ $title }}...
        </div>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg id="clear-selection-icon" class="hidden w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <svg id="dropdown-icon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        <div id="elts-dropdown" class="hidden absolute z-10 w-full mt-1 bg-gray-800 border border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            @foreach($elts as $elt)
                <div class="elts-option px-4 py-2 text-white hover:bg-gray-700 cursor-pointer flex items-center" data-value="{{ $elt->id }}" data-name="{{ $elt->name }}">
                    @if ($selected_elts)
                        @if($selected_elts->contains($elt))
                            <input type="checkbox" class="elt-checkbox mr-3 rounded border-gray-600 bg-gray-700 text-indigo-600 focus:ring-indigo-500" value="{{ $elt->id }}" checked>
                        @else
                            <input type="checkbox" class="elt-checkbox mr-3 rounded border-gray-600 bg-gray-700 text-indigo-600 focus:ring-indigo-500" value="{{ $elt->id }}">
                        @endif
                    @else
                        <input type="checkbox" class="elt-checkbox mr-3 rounded border-gray-600 bg-gray-700 text-indigo-600 focus:ring-indigo-500" value="{{ $elt->id }}">
                    @endif
                    
                    <span>{{ $elt->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @error('elts')
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
    @if (!$no_desc)
        <p class="mt-1 text-sm text-gray-400">Click to select multiple {{ $title }}</p>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const fieldName = document.getElementById('field_name').value;
    const title = '{{ $title }}';
    // Genders dropdown functionality
    const elts_display = document.getElementById('selected_elts');
    const dropdownIcon = document.getElementById('dropdown-icon');
    const clearSelectionIcon = document.getElementById('clear-selection-icon');
    const eltsDropdown = document.getElementById('elts-dropdown');
    const eltOptions = document.querySelectorAll('.elts-option');
    const eltCheckboxes = document.querySelectorAll('.elt-checkbox');
    const editable = document.getElementById('editable').value === 'true';
    let selectedElts = Array.from(eltCheckboxes).filter(checkbox => checkbox.checked).map(checkbox => {
        if (checkbox.checked) {
            return { value: checkbox.value, name: checkbox.closest('.elts-option').dataset.name };
        }
    })
    updateInputDisplay();

    // Toggle dropdown
    elts_display.addEventListener('click', function() {
        eltsDropdown.classList.toggle('hidden');
        dropdownIcon.classList.toggle('rotate-180');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!elts_display.contains(e.target) && !eltsDropdown.contains(e.target)) {
            eltsDropdown.classList.add('hidden');
            dropdownIcon.classList.remove('rotate-180');
        }
    });

    // Handle element selection
    eltOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            const checkbox = this.querySelector('.elt-checkbox');
            const value = this.dataset.value;
            const name = this.dataset.name;

            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                if (!selectedElts.find(g => g.value === value)) {
                    selectedElts.push({ value: value, name: name });
                }
            } else {
                selectedElts = selectedElts.filter(g => g.value !== value);
            }
            if (selectedElts.length === 0) {
                clearSelectionIcon.classList.add('hidden');
            } else {
                clearSelectionIcon.classList.remove('hidden');
            }
            updateInputDisplay();
        });
    });

    // Handle checkbox clicks directly
    eltCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    function updateInputDisplay() {
        if (selectedElts.length === 0) {
            elts_display.innerText = 'Select ' + title + '...';
        } else {
            if (editable) {
                elts_display.innerHTML = selectedElts.map(elt => 
                    `<span id='${title}_${elt.value}' class='inline-flex items-center mx-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>${elt.name}<svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg></span>`).join('');
            } else {
                elts_display.innerHTML = selectedElts.map(elt => 
                    `<span id='${title}_${elt.value}' class='inline-flex items-center mx-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800'>${elt.name}</span>`).join('');
            }
        }
        
        // Update the hidden input with the IDs
        updateHiddenInput();
    }

    function updateHiddenInput() {
        // Remove existing hidden input if it exists
        const existingHidden = document.getElementById('selected_elt_ids');
        if (existingHidden) {
            existingHidden.remove();
        }
        
        // Create new hidden input with the selected IDs
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.id = 'selected_elt_ids';
        hiddenInput.name = fieldName;
        hiddenInput.value = selectedElts.map(elt => elt.value).join(',');
        

        eltsDropdown.append(hiddenInput);

    }

    
});
</script>