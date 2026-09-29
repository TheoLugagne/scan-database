@props(['title' => 'Search', 'elts' => [], 'type', 'name' => 'search'])

@php
$type_list = [
    "radio" => 'radio',
    "multiselect" => 'multiselect',
    "checkbox" => 'checkbox',
    "select" => 'select',
];

if (!array_key_exists($type, $type_list)) {
    throw new Exception("Invalid type: " . $type);
}

$rawFilterValue = request($name, '');
if (is_array($rawFilterValue)) {
    $rawFilterValue = implode(',', array_filter($rawFilterValue, fn ($value) => $value !== null && $value !== ''));
}
$rawFilterValue = (string) $rawFilterValue;

$selectedIds = array_values(array_filter(array_map('intval', explode(',', $rawFilterValue))));
$selectedElts = collect($elts)->filter(function ($elt) use ($selectedIds) {
    return isset($elt->id) && in_array((int) $elt->id, $selectedIds, true);
})->values();
@endphp

<div class="filter-section px-1 pt-2">
    <input type="hidden" id="filter-section-value-{{ $name }}" value="{{ $rawFilterValue }}">
    <div class="filter-section-title text-center">
        <span class="text-indigo-300"> {{ $title }} </span>
    </div>
    <div class="filter-section-content">
         @switch($type)
            @case('multiselect')
                <x-m2m.pills_selector :elts="$elts" :selected_elts="$selectedElts" :title="$title" :field_name="$name" :editable="true" :no_title="true" :no_desc="true" :summary="true" />
                @break
            @case('radio')
                <x-m2o.selector :elts="$elts" :selected_elt="$rawFilterValue" :title="$title" :field_name="$name" :no_title="true" :no_desc="true" :empty_allowed="true" />
                @break
         @endswitch
    </div>
</div>

<script>
(function() {
    const fieldName = @json($name);
    const filterSectionValueInput = document.getElementById('filter-section-value-' + fieldName);
    if (!filterSectionValueInput) return;

    const filterSection = filterSectionValueInput.closest('.filter-section');
    const hiddenInput = filterSection
        ? filterSection.querySelector('input[type="hidden"][name="' + fieldName + '"]')
        : null;
    if (!hiddenInput) return;

    hiddenInput.addEventListener('change', function() {
        const newValue = hiddenInput.value || '';
        const oldValue = filterSectionValueInput.value || '';
        if (oldValue === newValue) return;

        filterSectionValueInput.value = newValue;
        if (typeof window.triggerFilterChange === 'function') {
            window.triggerFilterChange();
        }
    });
})();
</script>
