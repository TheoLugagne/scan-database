<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white">
            Welcome to {{ config('app.name') }}
        </h2>
    </x-slot>

    <div class="text-center">
        <h2 class="text-4xl font-extrabold text-white sm:text-5xl sm:tracking-tight lg:text-6xl">
            Track Your Scans
        </h2>
        <p class="mt-5 text-xl text-gray-400">
            Keep track of all your favorite scans chapters in one place
        </p>
        <div class="mt-8 flex justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Get Started
                </a>
                <a href="{{ route('login') }}" 
                    class="ml-4 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700">
                    Sign In
                </a>
            @endauth
        </div>
    </div>

    <!-- Features Section -->
    <div class="mt-20">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div class="pt-6 h-full">
                <div class="flow-root bg-gray-800 rounded-lg px-6 pb-8 h-full flex flex-col">
                    <div class="-mt-6 flex-grow">
                        <div class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                        </div>
                        <h3 class="mt-8 text-lg font-medium text-white tracking-tight">Track Progress</h3>
                        <p class="mt-5 text-base text-gray-400">
                            Keep track of which chapter you're on for each scan you're reading
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-6 h-full">
                <div class="flow-root bg-gray-800 rounded-lg px-6 pb-8 h-full flex flex-col">
                    <div class="-mt-6 flex-grow">
                        <div class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="mt-8 text-lg font-medium text-white tracking-tight">Organize Collection</h3>
                        <p class="mt-5 text-base text-gray-400">
                            Manage all your scans in one convenient location
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-6 h-full">
                <div class="flow-root bg-gray-800 rounded-lg px-6 pb-8 h-full flex flex-col">
                    <div class="-mt-6 flex-grow">
                        <div class="inline-flex items-center justify-center p-3 bg-indigo-500 rounded-md shadow-lg">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <h3 class="mt-8 text-lg font-medium text-white tracking-tight">Quick Access</h3>
                        <p class="mt-5 text-base text-gray-400">
                            Save links to easily return to where you left off
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
