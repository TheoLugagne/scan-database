@props(['elt' => null, 'title' => 'Item', 'size' => "md"])

@if($elt != null)
    <div id="m2o_pill">
        <div class="flex flex-wrap">
            @if($size == "md")
                <span class="inline-flex items-center px-3 py-1 rounded-full text-lg border-2 border-{{ $elt->color() }} text-{{ $elt->color() }}">
                    {{ $elt->label() }}
                </span>
            @elseif($size == "sm")
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm border-2 border-{{ $elt->color() }} text-{{ $elt->color() }}">
                    {{ $elt->label() }}
                </span>
            @elseif($size == "lg")
                <span class="inline-flex items-center px-3 py-1 rounded-full text-lg border-2 border-{{ $elt->color() }} text-{{ $elt->color() }}">
                    {{ $elt->label() }}
                </span>
            @endif
        </div>
    </div>
@endif