<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Creare Campanie Nouă') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Detalii Campanie</h3>
                        <a href="{{ route('campaigns') }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium" wire:navigate>
                            &larr; Înapoi la Campanii
                        </a>
                    </div>

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="createCampaign" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nume Campanie</label>
                                <input type="text" wire:model="name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Ex: Campanie Newsletter Ianuarie">
                                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Data și ora
                                    lansării (opțional)</label>
                                <input type="datetime-local" wire:model="scheduled_at"
                                    min="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <p class="mt-1 text-xs text-slate-500">Lăsați gol pentru a trimite imediat ce porniți
                                    campania.</p>
                                @error('scheduled_at') <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="emailTemplateId" class="block text-sm font-medium text-gray-700">Șablon
                                    Email</label>
                                <select wire:model="emailTemplateId"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Selectează un șablon</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}
                                            ({{ $template->is_html ? 'HTML' : 'Text' }})</option>
                                    @endforeach
                                </select>
                                @error('emailTemplateId') <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="emailListId" class="block text-sm font-medium text-gray-700">Listă
                                    Emailuri</label>
                                <select wire:model="emailListId"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Selectează o listă</option>
                                    @foreach($emailLists as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }} ({{ $list->valid_emails }}
                                            emailuri valide)</option>
                                    @endforeach
                                </select>
                                @error('emailListId') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <p class="text-sm text-gray-600">
                                <strong>Notă:</strong> Campania va fi creată în starea "Pending". O vei putea porni din
                                pagina principală de campanii.
                            </p>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Creează Campanie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>