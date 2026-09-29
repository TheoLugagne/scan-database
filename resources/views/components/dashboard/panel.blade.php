@props([
    'title',
    'superuser' => false,
])

@if (! $superuser || auth()->user()?->role === 'admin')
    <section {{ $attributes->merge(['class' => 'bg-gray-800 rounded-lg shadow-lg p-6']) }}>
        <h3 class="text-lg font-semibold text-white">{{ $title }}</h3>
        <div class="mt-4 text-sm text-gray-400">
            {{ $slot }}
        </div>
    </section>
@endif
