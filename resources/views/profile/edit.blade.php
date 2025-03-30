<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-gray-800 shadow-md rounded-lg p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-gray-800 shadow-md rounded-lg p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-gray-800 shadow-md rounded-lg p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
