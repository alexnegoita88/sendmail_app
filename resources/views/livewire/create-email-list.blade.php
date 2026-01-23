<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Creare Listă Manuală') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Nume Listă Nouă</h3>
                        <a href="{{ route('email-lists.index') }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            &larr; Înapoi la Liste
                        </a>
                    </div>

                    <form wire:submit.prevent="createList" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nume Listă</label>
                            <input type="text" wire:model="name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Ex: Lista Contacte VIP">
                            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Creează Listă
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>