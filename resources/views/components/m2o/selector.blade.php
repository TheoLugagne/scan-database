@props(['elts' => [], 'selected_elt' => null, 'empty_allowed' => false, 'title' => 'Items', 'field_name' => 'item_ids', 'no_title' => false, 'no_desc' => false])

<div id="m2o_selector_{{ $field_name }}">
    @if (!$no_title)
        <label class="block text-base font-medium text-gray-300">{{ $title }}</label>
    @endif
    <div class="relative mt-2">
        <div id="selected_elt_{{ $field_name }}" class="selector-display block w-full whitespace-nowrap rounded-md bg-gray-800 border border-gray-600 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 pr-8 cursor-pointer @error('elts') border-red-500 @enderror">
            Select {{ $title }}...
        </div>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        <div id="elt-dropdown-{{ $field_name }}" class="hidden absolute z-10 w-full mt-1 bg-gray-800 border border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            @if ($empty_allowed)
                <div class="elt-option px-4 py-2 text-white hover:bg-gray-700 cursor-pointer flex items-center" data-value="" data-name="Nothing selected">
                    <span class="mr-3 rounded border-gray-600 focus:ring-indigo-500" value="">Nothing selected</span>
                </div>
            @endif
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
    @if (!$no_desc)
        <p class="mt-1 text-sm text-gray-400">Click to select a {{ $title }}</p>
    @endif
    <input type="hidden" id="m2o_{{ $field_name }}" name="{{ $field_name }}" value="{{ is_object($selected_elt) ? $selected_elt->value : $selected_elt }}"/> 
</div>

<script>
(function() {
    const title = @json($title);
    const elt_display = document.getElementById('selected_elt_{{ $field_name }}');
    const eltDropdown = document.getElementById('elt-dropdown-{{ $field_name }}');
    const container = document.getElementById('m2o_selector_{{ $field_name }}');
    const hiddenInput = document.getElementById('m2o_{{ $field_name }}');
    if (!container || !elt_display || !hiddenInput) return;
    const eltOptions = container.querySelectorAll('.elt-option');
    
    const fieldName = @json($field_name);
    let selectedElt = @json(is_object($selected_elt) ? $selected_elt->value : $selected_elt);
    let ready = false;

    function updateInputDisplay() {
        const value = selectedElt ? String(selectedElt) : '';
        if (!value) {
            elt_display.textContent = 'Select ' + title + '...';
            hiddenInput.value = '';
        } else {
            const selectedOption = Array.from(eltOptions).find(option => option.dataset.value === value);
            const displayName = selectedOption ? selectedOption.dataset.name : value;
            elt_display.textContent = displayName;
            hiddenInput.value = value;
        }
    }

    function publishChange() {
        if (!ready) return;
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function setSelectedValue(value) {
        ready = false;
        selectedElt = value || '';
        updateInputDisplay();
        ready = true;
    }

    updateInputDisplay();
    ready = true;

    window.filterSetters = window.filterSetters || {};
    window.filterSetters[fieldName] = setSelectedValue;

    elt_display.addEventListener('click', function() {
        eltDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!elt_display.contains(e.target) && !eltDropdown.contains(e.target)) {
            eltDropdown.classList.add('hidden');
        }
    });

    eltOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            selectedElt = this.dataset.value;
            updateInputDisplay();
            publishChange();
        });
    });
})();
</script>