<div class="space-y-6">
    <!-- Upload Form -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Încărcare Fișier</h3>
            
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

            <form wire:submit.prevent="uploadFile" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nume Listă</label>
                    <input 
                        type="text" 
                        wire:model="name"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Ex: Lista Clienti Noi"
                    >
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700">Fișier (Excel, CSV sau JSON)</label>
                    <input 
                        type="file" 
                        wire:model="file"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    @error('file') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        wire:disabled="$wire.uploading"
                    >
                        <svg wire:loading wire:target="uploadFile" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ $uploading ? 'Se încarcă...' : 'Încarcă Fișier' }}
                    </button>

                    @if ($uploading)
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $uploadProgress }}%"></div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Email Lists -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Liste Încărcate</h3>
            
            @if ($emailLists->isEmpty())
                <p class="text-gray-500">Nu există liste de emailuri încărcate.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip Fișier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emailuri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($emailLists as $list)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $list->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($list->file_type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $list->valid_emails }} / {{ $list->total_emails }} valid
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($list->status === 'completed') bg-green-100 text-green-800
                                            @elseif($list->status === 'processing') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($list->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button 
                                            wire:click="deleteList({{ $list->id }})"
                                            onclick="return confirm('Sigur doriți să ștergeți această listă?')"
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
                    {{ $emailLists->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
