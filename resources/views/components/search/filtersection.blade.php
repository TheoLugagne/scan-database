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
@endphp

<div class="filter-section px-2 pt-2">
    <div class="filter-section-title text-center">
        <span class="text-indigo-300"> {{ $title }} </span>
    </div>
    <div class="filter-section-content">
        <!-- component -->
         @switch($type)
            @case('multiselect')
                <x-m2m.pills_selector :elts="$elts" :title="$title" :field_name="$name" :editable="true" :no_title="true" :no_desc="true" />
                @break
            @case('radio')
                <x-m2o.selector :elts="$elts" :title="$title" :field_name="$name" :no_title="true" :no_desc="true" />
                @break
         @endswitch
    </div>
</div>
