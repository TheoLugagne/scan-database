@props(["type" => ['UserScanProgress', 'Scan']])

@php
use App\Models\Scan;
use App\Models\UserScanProgress;

$type_list = [
    'UserScanProgress' => UserScanProgress::class,
    'Scan' => Scan::class,
];

foreach ($type as $t) {
    if (!array_key_exists($t, $type_list)) {
        throw new Exception("Invalid type: " . $t);
    }
}

$filter_sections_data = [];
foreach ($type as $t) {
    $filter_sections_data = array_merge($filter_sections_data, $type_list[$t]::getFilterSectionsData());
}

@endphp
<div class="filter-panel m-3 text-base absolute top-0 left-0 right-0 z-50 bg-gray-800 border border-gray-600 rounded-lg shadow-lg overflow-visible flex flex-wrap gap-4">
    @foreach ($filter_sections_data as $loopIndex => $nameAndData)
        @php
            [$name, $filter_section_data] = [$loopIndex, $nameAndData];
        @endphp
        <div class="flex-1 flex">
            <div class="flex-1">
                <x-search.filtersection :title="$filter_section_data['title']" :elts="$filter_section_data['elts']" :type="$filter_section_data['type']" :name="$name" />
            </div>
            @if(!$loop->last)
                <div class="border-r border-gray-600 self-center" style="height:60%"></div>
            @endif
        </div>
    @endforeach
</div>