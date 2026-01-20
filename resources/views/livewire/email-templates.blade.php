<div class="space-y-6">
    <!-- Template Form -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ $editingTemplateId ? 'Editare Șablon' : 'Creare Șablon Email' }}
            </h3>
            
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="saveTemplate" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nume Șablon</label>
                        <input 
                            type="text" 
                            wire:model="name"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Ex: Newsletter Lunar"
                        >
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subiect Email</label>
                        <input 
                            type="text" 
                            wire:model="subject"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Ex: Oferta Specială pentru Abonați"
                        >
                        @error('subject') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip Conținut</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="isHtml" value="1" class="form-radio">
                            <span class="ml-2">HTML</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="isHtml" value="0" class="form-radio">
                            <span class="ml-2">Text Simplu</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Conținut Email</label>
                    <div class="mt-1">
                        @if($isHtml)
                            <textarea 
                                wire:model="content"
                                rows="12"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Introduceți conținutul HTML al emailului..."
                            ></textarea>
                        @else
                            <textarea 
                                wire:model="content"
                                rows="12"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Introduceți conținutul text al emailului..."
                            ></textarea>
                        @endif
                    </div>
                    @error('content') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Puteți folosi următoarele placeholder-uri: {name}, {email}, {date}, {campaign_name}
                    </div>
                    <div class="flex space-x-3">
                        @if($editingTemplateId)
                            <button 
                                type="button"
                                wire:click="resetForm"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Anulează
                            </button>
                        @endif
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ $editingTemplateId ? 'Actualizează Șablon' : 'Salvează Șablon' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview -->
    @if($content)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Previzualizare Email</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                    <div class="bg-white rounded-lg p-6 max-w-2xl mx-auto">
                        <h4 class="font-bold text-lg mb-2">{{ $subject ?: 'Subiect Email' }}</h4>
                        <div class="border-t border-gray-200 pt-4">
                            @if($isHtml)
                                <div>{!! $content !!}</div>
                            @else
                                <pre class="whitespace-pre-wrap">{{ $content }}</pre>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Templates List -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Șabloane Salvate</h3>
            
            @if ($templates->isEmpty())
                <p class="text-gray-500">Nu există șabloane create.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subiect</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($templates as $template)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $template->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($template->subject, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $template->is_html ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $template->is_html ? 'HTML' : 'Text' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button 
                                            wire:click="editTemplate({{ $template->id }})"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Editează
                                        </button>
                                        <button 
                                            wire:click="deleteTemplate({{ $template->id }})"
                                            onclick="return confirm('Sigur doriți să ștergeți acest șablon?')"
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
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
