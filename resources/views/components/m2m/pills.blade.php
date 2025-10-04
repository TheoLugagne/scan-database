@props(['elts' => [], 'title' => 'Items'])

@if($elts->count() > 0)
    <div id="m2m_pills" class="my-2">
        <div class="flex flex-wrap gap-2">
            @foreach($elts as $elt)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ $elt->name }}
                </span>
            @endforeach
        </div>
    </div>
@else
    <div class="my-2">
        <p class="text-gray-500">No {{ $title }}</p>
    </div>
@endif