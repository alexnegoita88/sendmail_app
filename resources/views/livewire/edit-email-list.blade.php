<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editare Listă: {{ $list->name }}
            </h2>
            <a href="{{ route('email-lists.index') }}"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                &larr; Înapoi la Liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 relative">
                            {{ session('message') }}
                        </div>
                    @endif

                    <!-- Search & Stats -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-1/2">
                            <input wire:model.live.debounce.300ms="search" type="text"
                                placeholder="Caută după nume sau email..."
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div class="text-sm text-gray-500">
                            Total: <strong>{{ $list->total_emails }}</strong> | Valide:
                            <strong>{{ $list->valid_emails }}</strong>
                        </div>
                    </div>

                    <!-- Add New Contact Form -->
                    <div class="bg-gray-50 p-4 rounded-md mb-6 border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Adaugă Contact Nou</h4>
                        <form wire:submit.prevent="addContact" class="flex gap-4 items-start">
                            <div class="flex-1">
                                <input wire:model="newContactName" type="text" placeholder="Nume (opțional)"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('newContactName') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex-1">
                                <input wire:model="newContactEmail" type="email" placeholder="Email (obligatoriu)"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('newContactEmail') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none h-10">
                                Adaugă
                            </button>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nume</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data Adăugării</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acțiuni</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($contacts as $contact)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($editingContactId === $contact->id)
                                                <input wire:model="editName" type="text"
                                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                                            @else
                                                <div class="text-sm font-medium text-gray-900">{{ $contact->name ?: '-' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($editingContactId === $contact->id)
                                                <input wire:model="editEmail" type="email"
                                                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                                                @error('editEmail') <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            @else
                                                <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $contact->created_at ? $contact->created_at->format('d.m.Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($editingContactId === $contact->id)
                                                <button wire:click="saveContact"
                                                    class="text-green-600 hover:text-green-900 mr-2">Salvează</button>
                                                <button wire:click="cancelEdit"
                                                    class="text-gray-600 hover:text-gray-900">Anulează</button>
                                            @else
                                                <button wire:click="editContact({{ $contact->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2">Editează</button>
                                                <button wire:click="deleteContact({{ $contact->id }})"
                                                    onclick="return confirm('Sigur dorești să ștergi acest contact?')"
                                                    class="text-red-600 hover:text-red-900">Șterge</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nu am găsit contacte.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $contacts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>