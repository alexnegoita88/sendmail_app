// Email Template Editor with GrapesJS - Official Implementation
export function initGrapesJSEditor(mjmlContent = '', editorData = {}) {
    // Clean up existing editor
    if (window.grapesJSEditor) {
        try {
            window.grapesJSEditor.destroy();
        } catch (e) {
            console.warn('Error destroying previous editor:', e);
        }
        window.grapesJSEditor = null;
    }

    // Check if container exists
    const container = document.querySelector('#grapesjs-editor');
    if (!container) {
        console.error('GrapesJS container #grapesjs-editor not found');
        return null;
    }

    try {
        // Initialize GrapesJS according to official documentation
        window.grapesJSEditor = window.grapesjs.init({
            container: '#grapesjs-editor',
            height: '600px',
            width: 'auto',
            // Disable storage for memory-only operation
            storageManager: {
                type: 'memory'
            },
            // Disable asset manager for simplicity
            assetManager: {
                assets: [],
                upload: false,
                uploadText: 'Drop files here or click to upload'
            },
            // Load plugins
            plugins: [window.grapesjs.presets.newsletter, window.grapesjs.presets.mjml],
            pluginsOpts: {
                [window.grapesjs.presets.newsletter]: {
                    // Newsletter preset options
                    blocks: ['text', 'image', 'button', 'divider', 'spacer', 'social', 'hero', 'one-column', 'two-columns', 'three-columns'],
                    blockCategories: {
                        'Basic': ['text', 'image', 'button', 'link', 'divider', 'spacer'],
                        'Layout': ['one-column', 'two-columns', 'three-columns'],
                        'Media': ['hero', 'social']
                    }
                },
                [window.grapesjs.presets.mjml]: {
                    // MJML export options
                }
            },
            // Canvas configuration
            canvas: {
                styles: [
                    'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap'
                ]
            },
            // Disable panels we don't need
            panels: {
                defaults: [
                    {
                        id: 'layers',
                        el: '.panel__right',
                        resizable: {
                            maxDim: 350,
                            minDim: 200,
                            tc: 0,
                            cl: 1,
                            cr: 0,
                            bc: 0,
                            keyWidth: 'flex-basis',
                        },
                    },
                    {
                        id: 'panel-switcher',
                        el: '.panel__switcher',
                        buttons: [
                            {
                                id: 'show-layers',
                                active: true,
                                label: 'Layers',
                                command: 'show-layers',
                                togglable: false,
                            },
                            {
                                id: 'show-style',
                                active: false,
                                label: 'Styles',
                                command: 'show-styles',
                                togglable: false,
                            },
                            {
                                id: 'show-blocks',
                                active: false,
                                label: 'Blocks',
                                command: 'show-blocks',
                                togglable: false,
                            }
                        ],
                    }
                ]
            }
        });

        // Load existing content if provided
        if (mjmlContent && mjmlContent.trim()) {
            try {
                window.grapesJSEditor.setComponents(mjmlContent);
            } catch (e) {
                console.warn('Error loading MJML content:', e);
                // Try to set as plain HTML if MJML fails
                try {
                    window.grapesJSEditor.setComponents(`<mj-body><mj-container><mj-section><mj-column><mj-text>${mjmlContent}</mj-text></mj-column></mj-section></mj-container></mj-body>`);
                } catch (e2) {
                    console.error('Error setting fallback content:', e2);
                }
            }
        }

        // Load editor data if provided
        if (editorData && typeof editorData === 'object' && Object.keys(editorData).length > 0) {
            try {
                window.grapesJSEditor.loadProjectData(editorData);
            } catch (e) {
                console.warn('Error loading editor data:', e);
            }
        }

        // Listen for content changes and update Livewire
        window.grapesJSEditor.on('component:update component:add component:remove style:update', function() {
            const html = window.grapesJSEditor.getHtml();
            const projectData = window.grapesJSEditor.getProjectData();

            // Update Livewire component properties
            if (window.livewire && window.livewire.find) {
                const component = window.livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                if (component) {
                    component.set('mjmlContent', html);
                    component.set('editorData', projectData);
                }
            }
        });

        console.log('GrapesJS editor initialized successfully');
        return window.grapesJSEditor;

    } catch (error) {
        console.error('Failed to initialize GrapesJS editor:', error);
        return null;
    }
}

// Listen for Livewire events to initialize editor
document.addEventListener('livewire:loaded', function () {
    // Listen for the custom event dispatched by Livewire when visual editor is toggled
    Livewire.on('init-grapesjs-editor', function() {
        // Small delay to ensure DOM is updated
        setTimeout(function() {
            initGrapesJSEditor('', {});
        }, 100);
    });
});

// Global function for easy access
window.initGrapesJSEditor = initGrapesJSEditor;
