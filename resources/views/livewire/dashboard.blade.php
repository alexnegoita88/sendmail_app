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
