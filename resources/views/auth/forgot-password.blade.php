<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            {{ __('Forgot Password') }}
        </h2>
    </x-slot>

    <div class="bg-gray-800 shadow-md rounded-lg p-6">
        <div class="mb-4 text-sm text-gray-300">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-200" />
                <x-text-input id="email" 
                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <x-primary-button>
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>

                <a href="{{ route('login') }}" 
                    class="text-sm text-gray-300 hover:text-white underline">
                    {{ __('Back to Login') }}
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
