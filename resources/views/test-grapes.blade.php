<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test GrapesJS Editor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Editor GrapesJS cu NPM Packages</h3>

                    <!-- GrapesJS Container -->
                    <div id="grapesjs-test-editor" class="border border-gray-300 rounded-lg" style="height: 600px;"></div>

                    <!-- Debug Info -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Debug Information:</h4>
                        <div id="debug-info" class="text-sm text-gray-600">
                            <p>Se încarcă GrapesJS...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Se inițializează GrapesJS cu NPM packages...');

            const debugInfo = document.getElementById('debug-info');

            // Verifică dacă GrapesJS este disponibil
            if (typeof window.grapesjs === 'undefined') {
                debugInfo.innerHTML = '<p class="text-red-600">❌ GrapesJS nu este încărcat!</p>';
                return;
            }

            debugInfo.innerHTML = '<p class="text-green-600">✅ GrapesJS este încărcat</p>';

            // Verifică container-ul
            const container = document.getElementById('grapesjs-test-editor');
            if (!container) {
                debugInfo.innerHTML += '<p class="text-red-600">❌ Container-ul nu a fost găsit!</p>';
                return;
            }

            debugInfo.innerHTML += '<p class="text-green-600">✅ Container-ul a fost găsit</p>';

            try {
                debugInfo.innerHTML += '<p class="text-blue-600">🔄 Se inițializează GrapesJS...</p>';

                // Inițializare GrapesJS cu preset newsletter
                const editor = window.grapesjs.init({
                    container: '#grapesjs-test-editor',
                    height: '600px',
                    width: 'auto',
                    // Dezactivează storage pentru test
                    storageManager: {
                        type: 'memory'
                    },
                    // Dezactivează asset manager
                    assetManager: {
                        assets: [],
                        upload: false
                    },
                    // Încarcă doar preset newsletter (fără MJML pentru moment)
                    plugins: [window.grapesjs.presets.newsletter],
                    pluginsOpts: {
                        [window.grapesjs.presets.newsletter]: {
                            // Blocuri pentru email
                            blocks: [
                                'text', 'image', 'button', 'divider', 'spacer',
                                'hero', 'one-column', 'two-columns', 'three-columns'
                            ],
                            blockCategories: {
                                'Basic': ['text', 'image', 'button', 'link', 'divider', 'spacer'],
                                'Layout': ['one-column', 'two-columns', 'three-columns'],
                                'Media': ['hero']
                            }
                        }
                    },
                    // Configurare canvas
                    canvas: {
                        styles: [
                            'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap'
                        ]
                    }
                });

                debugInfo.innerHTML += '<p class="text-green-600">✅ GrapesJS inițializat cu succes!</p>';
                debugInfo.innerHTML += '<p class="text-blue-600">🎉 Editorul ar trebui să fie vizibil deasupra</p>';

                // Test funcționalitate
                editor.on('component:add', function() {
                    console.log('Component adăugat în GrapesJS');
                });

                editor.on('block:drag:stop', function() {
                    console.log('Bloc plasat în canvas');
                });

            } catch (error) {
                debugInfo.innerHTML += `<p class="text-red-600">❌ Eroare la inițializarea GrapesJS: ${error.message}</p>`;
                console.error('Eroare GrapesJS:', error);
            }
        });
    </script>
</x-app-layout>
