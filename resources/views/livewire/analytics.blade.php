<div class="space-y-6">
    <!-- Analytics Controls -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistici și Analize</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label for="campaignFilter" class="block text-sm font-medium text-gray-700 mb-2">Filtrare Campanie</label>
                    <select 
                        wire:model="selectedCampaignId"
                        id="campaignFilter"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option value="">Toate Campaniile</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-600">Emailuri Trimise</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $analytics['total_emails_sent'] ?? 0 }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-600">Emailuri Deschise</div>
                            <div class="text-2xl font-bold text-green-900">{{ $analytics['total_emails_opened'] ?? 0 }}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-red-600">Emailuri Eșuate</div>
                            <div class="text-2xl font-bold text-red-900">{{ $analytics['total_emails_failed'] ?? 0 }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-purple-600">Rată Deschidere</div>
                            <div class="text-2xl font-bold text-purple-900">{{ $analytics['open_rate'] ?? 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Analytics -->
    @if($selectedCampaignId && isset($analytics['campaign']))
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">
                    Analiză Campanie: {{ $analytics['campaign']->name }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-600">Total Emailuri</div>
                        <div class="text-xl font-bold text-gray-900">{{ $analytics['total_emails'] }}</div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-blue-600">Trimise</div>
                        <div class="text-xl font-bold text-blue-900">{{ $analytics['emails_sent'] }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-green-600">Deschise</div>
                        <div class="text-xl font-bold text-green-900">{{ $analytics['emails_opened'] }}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-red-600">Eșuate</div>
                        <div class="text-xl font-bold text-red-900">{{ $analytics['emails_failed'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Rate Performanță</h5>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Rată Deschidere</span>
                                <span class="text-sm font-bold text-green-600">{{ $analytics['open_rate'] }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Rată Livrare</span>
                                <span class="text-sm font-bold text-blue-600">{{ $analytics['delivery_rate'] }}%</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Distribuție Dispozitive</h5>
                        <div class="space-y-2">
                            @foreach($analytics['device_data'] as $device)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ ucfirst($device->device) }}</span>
                                    <span class="text-sm font-bold text-purple-600">{{ $device->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Overall Analytics -->
    @if(!$selectedCampaignId)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Statistici Generale</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-indigo-50 p-6 rounded-lg">
                        <div class="text-sm font-medium text-indigo-600">Total Campanii</div>
                        <div class="text-3xl font-bold text-indigo-900">{{ $analytics['total_campaigns'] }}</div>
                    </div>
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="text-sm font-medium text-blue-600">Emailuri Trimise</div>
                        <div class="text-3xl font-bold text-blue-900">{{ $analytics['total_emails_sent'] }}</div>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <div class="text-sm font-medium text-green-600">Emailuri Deschise</div>
                        <div class="text-3xl font-bold text-green-900">{{ $analytics['total_emails_opened'] }}</div>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <div class="text-sm font-medium text-purple-600">Rată Deschidere</div>
                        <div class="text-3xl font-bold text-purple-900">{{ $analytics['open_rate'] }}%</div>
                    </div>
                </div>

                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-4">Ultimele Campanii</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nume</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Șablon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trimise</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deschise</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rată</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($analytics['recent_campaigns'] as $campaign)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $campaign->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $campaign->emailTemplate->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $campaign->emails_sent }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $campaign->emails_opened }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $campaign->emails_sent > 0 ? round(($campaign->emails_opened / $campaign->emails_sent) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
