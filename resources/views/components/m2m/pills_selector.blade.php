@props(['elts' => [], 'selected_elts' => [], 'title' => 'Items', 'field_name' => 'item_ids', 'editable' => false, 'no_title' => false, 'no_desc' => false, 'summary' => false])

@php
    $selectedIds = collect($selected_elts)->map(function ($elt) {
        if (is_object($elt)) {
            return $elt->id;
        }
        if (is_array($elt)) {
            return $elt['id'] ?? null;
        }
        return $elt;
    })->filter(fn ($id) => $id !== null && $id !== '')->values();
    $selectedValue = $selectedIds->implode(',');
@endphp

<div id="m2m_pills_selector_{{ $field_name }}">
    @if (!$no_title)
        <label class="block text-base font-medium text-gray-300">{{ $title }}</label>
    @endif
    <div class="relative mt-2">
        <div id="selected_elts_{{ $field_name }}" class="selector-display block w-full rounded-md bg-gray-800 border border-gray-600 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base p-2 pr-14 cursor-pointer @if($summary) truncate @endif @error('elts') border-red-500 @enderror">
            Select {{ $title }}...
        </div>
        <div class="absolute inset-y-0 right-0 z-10 flex items-center pr-2 pointer-events-none">
            <button type="button" id="clear-selection-icon-{{ $field_name }}" class="clear-selection-icon hidden pointer-events-auto cursor-pointer rounded p-1 text-gray-400 hover:text-white" aria-label="Clear selection">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <svg id="dropdown-icon-{{ $field_name }}" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        <div id="elts-dropdown-{{ $field_name }}" class="hidden absolute z-10 w-full mt-1 bg-gray-800 border border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
            @foreach($elts as $elt)
                <div class="elts-option px-4 py-2 text-white hover:bg-gray-700 cursor-pointer flex items-center" data-value="{{ $elt->id }}" data-name="{{ $elt->name }}">
                    <input type="checkbox" class="elt-checkbox mr-3 rounded border-gray-600 bg-gray-700 text-indigo-600 focus:ring-indigo-500" value="{{ $elt->id }}" @checked($selectedIds->contains($elt->id) || $selectedIds->contains((string) $elt->id))>
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
    <input type="hidden" id="m2m_{{ $field_name }}" name="{{ $field_name }}" value="{{ $selectedValue }}">
</div>

<script>
(function() {
    const fieldName = @json($field_name);
    const title = @json($title);
    const editable = @json((bool) $editable);
    const summary = @json((bool) $summary);
    const container = document.getElementById('m2m_pills_selector_' + fieldName);
    if (!container) return;

    const eltsDisplay = document.getElementById('selected_elts_' + fieldName);
    const dropdownIcon = document.getElementById('dropdown-icon-' + fieldName);
    const clearSelectionIcon = document.getElementById('clear-selection-icon-' + fieldName);
    const eltsDropdown = document.getElementById('elts-dropdown-' + fieldName);
    const hiddenInput = document.getElementById('m2m_' + fieldName);
    const eltOptions = container.querySelectorAll('.elts-option');
    const eltCheckboxes = container.querySelectorAll('.elt-checkbox');
    let ready = false;
    let selectedElts = Array.from(eltCheckboxes).filter(checkbox => checkbox.checked).map(checkbox => {
        return { value: checkbox.value, name: checkbox.closest('.elts-option').dataset.name };
    });

    function updateHiddenInput() {
        const value = selectedElts.map(elt => elt.value).join(',');
        const changed = hiddenInput.value !== value;
        hiddenInput.value = value;
        if (ready && changed) {
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function updateInputDisplay() {
        if (selectedElts.length === 0) {
            eltsDisplay.textContent = 'Select ' + title + '...';
            clearSelectionIcon.classList.add('hidden');
        } else {
            clearSelectionIcon.classList.remove('hidden');
            if (summary) {
                eltsDisplay.textContent = selectedElts.length + ' selected';
            } else {
                eltsDisplay.innerHTML = selectedElts.map(elt => {
                    const icon = editable
                        ? `<svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>`
                        : '';
                    return `<span class="inline-flex items-center mx-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">${elt.name}${icon}</span>`;
                }).join('');
            }
        }
        updateHiddenInput();
    }

    function setSelectedIds(value) {
        const ids = String(value || '').split(',').map(id => id.trim()).filter(Boolean);
        ready = false;
        selectedElts = [];
        eltCheckboxes.forEach(checkbox => {
            const option = checkbox.closest('.elts-option');
            const checked = ids.includes(checkbox.value);
            checkbox.checked = checked;
            if (checked) {
                selectedElts.push({ value: checkbox.value, name: option.dataset.name });
            }
        });
        updateInputDisplay();
        ready = true;
    }

    updateInputDisplay();
    ready = true;

    window.filterSetters = window.filterSetters || {};
    window.filterSetters[fieldName] = setSelectedIds;

    eltsDisplay.addEventListener('click', function() {
        eltsDropdown.classList.toggle('hidden');
        dropdownIcon.classList.toggle('rotate-180');
    });

    document.addEventListener('click', function(e) {
        if (!eltsDisplay.contains(e.target) && !eltsDropdown.contains(e.target)) {
            eltsDropdown.classList.add('hidden');
            dropdownIcon.classList.remove('rotate-180');
        }
    });

    clearSelectionIcon.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        setSelectedIds('');
        eltsDropdown.classList.add('hidden');
        dropdownIcon.classList.remove('rotate-180');
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    });

    eltOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            if (e.target.closest('.elt-checkbox')) {
                return;
            }
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
            updateInputDisplay();
        });
    });

    eltCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        checkbox.addEventListener('change', function() {
            const option = this.closest('.elts-option');
            const value = this.value;
            const name = option.dataset.name;
            if (this.checked) {
                if (!selectedElts.find(g => g.value === value)) {
                    selectedElts.push({ value: value, name: name });
                }
            } else {
                selectedElts = selectedElts.filter(g => g.value !== value);
            }
            updateInputDisplay();
        });
    });
})();
</script>
