<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Creare Șablon Email') }}
        </h2>
    </x-slot>

    @vite('resources/js/tinymce-init.js')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Detalii Șablon</h3>
                        <a href="{{ route('email-templates.index') }}"
                            class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            &larr; Înapoi la Șabloane
                        </a>
                    </div>
                    <form action="{{ route('email-templates.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nume Șablon</label>
                                <input type="text" name="name" id="name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Ex: Newsletter Lunar" required>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Subiect
                                    Email</label>
                                <input type="text" name="subject" id="subject"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Ex: Oferta Specială pentru Abonați" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="attachment" class="block text-sm font-medium text-gray-700">Atașament
                                (Opțional)</label>
                            <input type="file" name="attachment" id="attachment"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">Puteți atașa un fișier (PDF, imagine, doc, etc.) care
                                va fi trimis împreună cu email-ul.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tip Editor</label>
                            <div class="flex space-x-4">
                                <button type="button" onclick="switchEditor('simple')"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    Editor Simplu
                                </button>
                                <button type="button" onclick="switchEditor('tinymce')"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    Editor Avansat (TinyMCE)
                                </button>
                            </div>
                            <input type="hidden" name="is_html" id="is_html" value="1">
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Conținut Email</label>
                            <div class="mt-1">
                                <!-- TinyMCE Editor -->
                                <textarea id="tinymce-content" name="content" rows="12"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Introduceți conținutul HTML al emailului..."></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                Puteți folosi următoarele placeholder-uri: {name}, {email}, {date}, {campaign_name}
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('email-templates.index') }}"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Anulează
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Salvează Șablon
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>