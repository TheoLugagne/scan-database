@props(['elts' => null, 'selected' => null, 'field_name' => 'status', 'readonly' => false])

<div id="m2o_statusbar_{{ $field_name }}">
    <div class="flex flex-wrap border border-indigo-600 rounded-md mb-5">
        @foreach($elts as $elt)
            @php
                $isSelected = $selected && $selected->value === $elt->value;
            @endphp
            @if ($readonly)
                <span data-value="{{ $elt->value }}" data-name="{{ $elt->label() }}"
                class="status_elt flex-1 inline-flex items-center justify-center px-auto py-2.5 text-xs font-medium text-white {{ $loop->last ? 'rounded-r-md' : '' }} {{ $loop->first ? 'rounded-l-md' : '' }} {{ $isSelected ? 'bg-indigo-600' : 'bg-gray-600' }}">
                    {{ $elt->label() }}
                </span>
            @else
                <span data-value="{{ $elt->value }}" data-name="{{ $elt->label() }}"
                class="status_elt cursor-pointer flex-1 inline-flex items-center justify-center px-auto py-2.5 text-xs font-medium text-white hover:bg-indigo-600 hover:border-indigo-600 {{ $loop->last ? 'rounded-r-md' : '' }} {{ $loop->first ? 'rounded-l-md' : '' }} {{ $isSelected ? 'bg-indigo-600' : 'bg-gray-600' }}">
                    {{ $elt->label() }}
                </span>
            @endif
        @endforeach
    </div>
    <input type="hidden" id="status_{{ $field_name }}" name="{{ $field_name }}" value="{{ $selected?->value }}" />
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('m2o_statusbar_{{ $field_name }}');
    const statusElts = container.querySelectorAll('.status_elt');
    statusElts.forEach(elt => {
        elt.addEventListener('click', function() {
            if ({{ $readonly ? 'true' : 'false' }}) {
                return;
            }
            updateDisplay(this);
            updateHiddenInput(this);
        });
    });

    function updateDisplay(target) {
        statusElts.forEach(elt => {
            elt.classList.remove('bg-indigo-600');
            elt.classList.add('bg-gray-600');
        });
        target.classList.add('bg-indigo-600');
    }

    function updateHiddenInput(target) {
        document.getElementById('status_{{ $field_name }}').value = target.dataset.value;
    }
});
</script>