<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mail Sending Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bine ai venit, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600">Aici poți gestiona trimiterea de emailuri în masă, crea liste de destinatari și urmări performanța campaniilor tale.</p>
                </div>
            </div>

            <!-- Main Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- File Upload Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Încărcare Fișiere</h4>
                                <p class="text-sm text-gray-600">Încarcă fișiere Excel, JSON sau CSV cu adrese de email</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('file-upload') }}" class="text-blue-600 hover:text-blue-800 font-medium">Accesează</a>
                        </div>
                    </div>
                </div>

                <!-- Email Templates Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Șabloane Email</h4>
                                <p class="text-sm text-gray-600">Creează și gestionează șabloane de email HTML</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('email-templates') }}" class="text-green-600 hover:text-green-800 font-medium">Accesează</a>
                        </div>
                    </div>
                </div>

                <!-- Campaigns Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Campanii</h4>
                                <p class="text-sm text-gray-600">Gestionează campaniile de trimitere email</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('campaigns') }}" class="text-purple-600 hover:text-purple-800 font-medium">Accesează</a>
                        </div>
                    </div>
                </div>

                <!-- Analytics Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Statistici</h4>
                                <p class="text-sm text-gray-600">Urmărește performanța campaniilor tale</p>
                            </div>
                            <div class="bg-orange-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('analytics') }}" class="text-orange-600 hover:text-orange-800 font-medium">Accesează</a>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                @livewire('dashboard')

                <!-- System Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Status Sistem</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Server SMTP</span>
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Conectat</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Rate Limit</span>
                                <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">50/min</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Bază de Date</span>
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded">Funcțională</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
