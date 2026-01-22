/**
 * TinyMCE init – npm + Vite
 * GARANTAT să apară editorul
 */

// CORE
import tinymce from 'tinymce/tinymce';

// MODEL OBLIGATORIU
import 'tinymce/models/dom';

// ICONS + THEME
import 'tinymce/icons/default';
import 'tinymce/themes/silver';

// SKIN + CONTENT CSS (OBLIGATORIU)
import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/skins/content/default/content.css';

// PLUGINS
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/table';
// import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

// Funcție unică pentru inițializarea TinyMCE cu upload la server
function initTinyMCEEditor() {
    const textarea = document.getElementById('tinymce-content');
    if (!textarea) return;

    // Distrugem editorul dacă există deja
    if (tinymce.get('tinymce-content')) {
        tinymce.get('tinymce-content').destroy();
    }

    tinymce.init({
        target: textarea,
        license_key: 'gpl',
        height: 500,
        menubar: true,

        // 🔴 FOARTE IMPORTANT
        plugins: `
            advlist autolink lists link image charmap preview
            searchreplace visualblocks code fullscreen
            insertdatetime table wordcount
        `,

        toolbar: `
            undo redo | blocks |
            bold italic backcolor |
            alignleft aligncenter alignright alignjustify |
            bullist numlist outdent indent |
            removeformat | image
        `,

        // 🔴 OBLIGATORIU CÂND imporți CSS manual
        skin: false,
        content_css: false,
        
        // Handler pentru upload-ul de imagini la server
        images_upload_handler: function (blobInfo, success, failure) {
            const maxSize = 500 * 1024; // 500 KB
            const file = blobInfo.blob();

            if (file.size > maxSize) {
                failure('Imaginea este prea mare (max 500KB)');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            // RETURN fetch pentru TinyMCE
            return fetch('/upload-image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    return { url: data.url }; // returnăm obiectul așteptat de TinyMCE
                } else {
                    throw new Error(data.error || 'Upload failed');
                }
            })
            .catch(err => {
                failure(err.message);
            });
        }
    });
}

// expose global (Laravel / Blade)
window.initTinyMCEEditor = initTinyMCEEditor;

// Funcția pentru comutarea între tipuri de editor
window.switchEditor = function (type) {
    const isHtmlInput = document.getElementById('is_html');
    const container = document.getElementById('tinymce-content').parentElement;
    const currentContent = document.getElementById('tinymce-content').value;

    // Distrugem editorul dacă există
    if (tinymce.get('tinymce-content')) {
        tinymce.get('tinymce-content').destroy();
    }

    if (type === 'simple') {
        // Switch to simple editor
        const textarea = document.createElement('textarea');
        textarea.id = 'tinymce-content';
        textarea.name = 'content';
        textarea.rows = 12;
        textarea.className = 'block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
        textarea.placeholder = 'Introduceți conținutul text al emailului...';
        textarea.value = currentContent;

        container.replaceChild(textarea, document.getElementById('tinymce-content'));
        isHtmlInput.value = '0';
    } else {
        // Switch to TinyMCE
        const textarea = document.createElement('textarea');
        textarea.id = 'tinymce-content';
        textarea.name = 'content';
        textarea.rows = 12;
        textarea.className = 'block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
        textarea.placeholder = 'Introduceți conținutul HTML al emailului...';
        textarea.value = currentContent;

        container.replaceChild(textarea, document.getElementById('tinymce-content'));
        isHtmlInput.value = '1';
        initTinyMCEEditor();
    }
};

// Inițializăm editorul la încărcarea paginii
document.addEventListener('DOMContentLoaded', function() {
    const isHtmlInput = document.getElementById('is_html');
    if (isHtmlInput) {
        const isHtml = isHtmlInput.value === '1';
        if (isHtml) {
            initTinyMCEEditor();
        } else {
            // Setăm valoarea is_html la 0 pentru editor simplu
            isHtmlInput.value = '0';
        }
    } else {
        // Dacă nu există câmpul is_html (pagina de creare), inițializăm TinyMCE
        initTinyMCEEditor();
    }
});
