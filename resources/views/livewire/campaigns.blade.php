<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestionare Campanii') }}
        </h2>
    </x-slot>

    <div class="py-12" @if($runningCampaignId) wire:poll.2s @endif>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                <!-- New Campaign Button -->
                <div class="mb-6">
                    <a href="{{ route('campaigns.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Campanie Nouă
                    </a>
                </div>

                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('message') }}
                    </div>
                @endif


                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif


                <!-- Progress Bar for Running Campaigns -->
                @if($runningCampaignId)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📧 Procesare Campanie</h3>

                            <div class="mb-4">
                                <div class="flex justify-center items-center space-x-4 mb-4">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    <div class="text-lg font-medium text-gray-900">{{ $progressMessage }}</div>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-6">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-6 rounded-full transition-all duration-500 ease-out flex items-center justify-center text-white text-sm font-medium"
                                        style="width: {{ max($progressPercentage, 5) }}%">
                                        {{ $progressPercentage }}%
                                    </div>
                                </div>

                                <div class="text-center text-sm text-gray-500 mt-3">
                                    Procesează {{ $emailsProcessed }} din {{ $totalEmails }} emailuri
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-blue-600 animate-pulse" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </div>
                                    <div class="text-sm text-blue-800">
                                        <strong>Emailurile se trimit automat în fundal.</strong><br>
                                        Nu închide această pagină până când procesul se finalizează.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Campaigns List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Campanii</h3>

                        @if ($campaigns->isEmpty())
                            <p class="text-gray-500">Nu există campanii create.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nume</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Șablon</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Listă</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Emailuri</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($campaigns as $campaign)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $campaign->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $campaign->emailTemplate->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <a href="{{ route('email-lists.edit', $campaign->emailList) }}"
                                                        class="text-blue-600 hover:text-blue-900 hover:underline">
                                                        {{ $campaign->emailList->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $campaign->emails_sent }} / {{ $campaign->emailList->valid_emails }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getStatusColor($campaign->status) }}">
                                                        {{ ucfirst($campaign->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    @if($campaign->status === 'pending')
                                                        <button wire:click="startCampaign({{ $campaign->id }})"
                                                            wire:loading.attr="disabled"
                                                            class="text-green-600 hover:text-green-900 disabled:opacity-50 disabled:cursor-not-allowed">
                                                            <span wire:loading.remove
                                                                wire:target="startCampaign({{ $campaign->id }})">Pornește</span>
                                                            <span wire:loading wire:target="startCampaign({{ $campaign->id }})">
                                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-green-600"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                        stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                    </path>
                                                                </svg>
                                                                Se procesează...
                                                            </span>
                                                        </button>
                                                    @elseif($campaign->status === 'running')
                                                        <button wire:click="pauseCampaign({{ $campaign->id }})"
                                                            class="text-yellow-600 hover:text-yellow-900">
                                                            Pauză
                                                        </button>
                                                    @endif
                                                    <button wire:click="deleteCampaign({{ $campaign->id }})"
                                                        onclick="return confirm('Sigur doriți să ștergeți această campanie?')"
                                                        class="text-red-600 hover:text-red-900">
                                                        Șterge
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $campaigns->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>