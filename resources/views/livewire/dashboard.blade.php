<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 my-6">

    <!-- Analytics Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Statistici</h4>
                    <p class="text-sm text-gray-600">Urmărește performanța campaniilor tale</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('analytics') }}"
                    class="text-orange-600 hover:text-orange-800 font-medium">Accesează</a>
            </div>
        </div>
    </div>


    <!-- Quick Stats Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
        <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Statistici Rapide</h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['email_lists'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Liste Încărcate</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['templates'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Șabloane</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['campaigns'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Campanii</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['emails_sent'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Emailuri Trimise</div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
        <div class="p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Status Sistem</h4>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Server SMTP</span>
                    <span
                        class="px-2 py-1 text-xs font-semibold {{ $stats['system_status']['smtp_connected'] ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }} rounded">
                        {{ $stats['system_status']['smtp_connected'] ? 'Conectat' : 'Deconectat' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Rate Limit</span>
                    <span
                        class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">{{ $stats['system_status']['rate_limit'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Bază de Date</span>
                    <span
                        class="px-2 py-1 text-xs font-semibold {{ $stats['system_status']['database_connected'] ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }} rounded">
                        {{ $stats['system_status']['database_connected'] ? 'Funcțională' : 'Indisponibilă' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>