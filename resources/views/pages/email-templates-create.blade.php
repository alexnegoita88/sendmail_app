<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Creează Șablon Email') }}
            </h2>
            <a href="{{ route('email-templates.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                ← Înapoi la Șabloane
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Form for template data -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form id="template-form" method="POST" action="#" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nume Șablon</label>
                                <input type="text" id="name" name="name"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Ex: Newsletter Lunar" required>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Subiect Email</label>
                                <input type="text" id="subject" name="subject"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Ex: Oferta Specială pentru Abonați" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tip Conținut</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_html" value="1" checked
                                           class="form-radio">
                                    <span class="ml-2">HTML</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="is_html" value="0"
                                           class="form-radio">
                                    <span class="ml-2">Text Simplu</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- GrapesJS Editor -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Editor Vizual GrapesJS</h3>
                        <div class="flex space-x-3">
                            <button id="save-template"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                💾 Salvează Șablon
                            </button>
                        </div>
                    </div>

                    <!-- GrapesJS Container -->
                    <div id="grapesjs-editor" class="border border-gray-300 rounded-lg" style="height: 700px;"></div>

                    <!-- Hidden field for storing MJML content -->
                    <input type="hidden" id="mjml-content" name="mjml_content">
                    <input type="hidden" id="editor-data" name="editor_data">

                    <!-- Debug Info -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Status Editor:</h4>
                        <div id="editor-status" class="text-sm text-gray-600">
                            <p>Se încarcă editorul GrapesJS...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let grapesJSEditor = null;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inițializare editor GrapesJS pentru creare template...');

            const statusDiv = document.getElementById('editor-status');
            const saveButton = document.getElementById('save-template');
            const templateForm = document.getElementById('template-form');

            // Check if GrapesJS is available
            if (typeof window.grapesjs === 'undefined') {
                statusDiv.innerHTML = '<p class="text-red-600">❌ GrapesJS nu este încărcat!</p>';
                return;
            }

            statusDiv.innerHTML = '<p class="text-green-600">✅ GrapesJS este încărcat</p>';

            try {
                statusDiv.innerHTML += '<p class="text-blue-600">🔄 Se inițializează GrapesJS...</p>';

                // Initialize GrapesJS with newsletter preset (without MJML to avoid errors)
                grapesJSEditor = window.grapesjs.init({
                    container: '#grapesjs-editor',
                    height: '700px',
                    width: 'auto',
                    storageManager: { type: 'memory' },
                    assetManager: { assets: [], upload: false },
                    plugins: [window.grapesjs.presets.newsletter],
                    pluginsOpts: {
                        [window.grapesjs.presets.newsletter]: {
                            // Use default blocks (comment out to use all available)
                            // blocks: ['text', 'image', 'button', 'divider', 'spacer', 'link']
                        }
                    },
                    canvas: {
                        styles: [
                            'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap'
                        ]
                    }
                });

                statusDiv.innerHTML += '<p class="text-green-600">✅ GrapesJS inițializat cu succes!</p>';
                statusDiv.innerHTML += '<p class="text-blue-600">🎉 Editorul este gata de utilizat!</p>';

                // Listen for content changes
                grapesJSEditor.on('component:update component:add component:remove style:update', function() {
                    const html = grapesJSEditor.getHtml();
                    const projectData = grapesJSEditor.getProjectData();

                    // Update hidden fields
                    document.getElementById('mjml-content').value = html;
                    document.getElementById('editor-data').value = JSON.stringify(projectData);
                });

                // Save button handler
                saveButton.addEventListener('click', function(e) {
                    e.preventDefault();

                    const formData = new FormData(templateForm);
                    formData.append('mjml_content', document.getElementById('mjml-content').value);
                    formData.append('editor_data', document.getElementById('editor-data').value);
                    formData.append('content', document.getElementById('mjml-content').value); // For backward compatibility

                    // Here you would send the data to the server
                    console.log('Form data:', Object.fromEntries(formData));
                    alert('Șablon salvat! (Implementare server-side necesară)');

                    // Redirect back to templates list
                    // window.location.href = '{{ route("email-templates.index") }}';
                });

            } catch (error) {
                statusDiv.innerHTML += `<p class="text-red-600">❌ Eroare la inițializarea GrapesJS: ${error.message}</p>`;
                console.error('Eroare GrapesJS:', error);
            }
        });
    </script>
</x-app-layout>
