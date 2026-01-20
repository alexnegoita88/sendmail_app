<div class="space-y-6">
    <!-- Create Campaign Form -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Creare Campanie</h3>
            
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="createCampaign" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nume Campanie</label>
                    <input 
                        type="text" 
                        wire:model="name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Ex: Campanie Newsletter Ianuarie"
                    >
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="emailTemplateId" class="block text-sm font-medium text-gray-700">Șablon Email</label>
                        <select 
                            wire:model="emailTemplateId"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="">Selectează un șablon</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->is_html ? 'HTML' : 'Text' }})</option>
                            @endforeach
                        </select>
                        @error('emailTemplateId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="emailListId" class="block text-sm font-medium text-gray-700">Listă Emailuri</label>
                        <select 
                            wire:model="emailListId"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        >
                            <option value="">Selectează o listă</option>
                            @foreach($emailLists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }} ({{ $list->valid_emails }} emailuri valide)</option>
                            @endforeach
                        </select>
                        @error('emailListId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Asigură-te că ai un șablon și o listă de emailuri valide înainte de a crea campania.
                    </div>
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                    >
                        Creează Campanie
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Șablon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listă</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emailuri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($campaigns as $campaign)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $campaign->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $campaign->emailTemplate->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $campaign->emailList->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $campaign->emails_sent }} / {{ $campaign->emailList->valid_emails }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getStatusColor($campaign->status) }}">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @if($campaign->status === 'pending')
                                            <button 
                                                wire:click="startCampaign({{ $campaign->id }})"
                                                class="text-green-600 hover:text-green-900">
                                                Pornește
                                            </button>
                                        @elseif($campaign->status === 'running')
                                            <button 
                                                wire:click="pauseCampaign({{ $campaign->id }})"
                                                class="text-yellow-600 hover:text-yellow-900">
                                                Pauză
                                            </button>
                                        @endif
                                        <button 
                                            wire:click="deleteCampaign({{ $campaign->id }})"
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
