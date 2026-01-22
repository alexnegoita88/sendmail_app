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

function initTinyMCEEditor() {
    const textarea = document.getElementById('tinymce-content');
    if (!textarea) return;

    if (tinymce.get('tinymce-content')) {
        tinymce.get('tinymce-content').remove();
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
            removeformat
        `,

        // 🔴 OBLIGATORIU CÂND imporți CSS manual
        skin: false,
        content_css: false,
        images_upload_handler: function (blobInfo) {
            return new Promise((resolve, reject) => {
                const maxSize = 500 * 1024; // 500 KB

                if (blobInfo.blob().size > maxSize) {
                    reject('Imaginea este prea mare (max 500KB)');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function () {
                    resolve({ url: reader.result }); // <--- obiect cu url
                };
                reader.onerror = function () {
                    reject('Image conversion failed');
                };
                reader.readAsDataURL(blobInfo.blob());
            });
        }


    });
}

// expose global (Laravel / Blade)
window.initTinyMCEEditor = initTinyMCEEditor;

// auto init
document.addEventListener('DOMContentLoaded', initTinyMCEEditor);

// Funcția pentru comutarea între tipuri de editor
window.switchEditor = function (type) {
    if (type === 'simple') {
        // Switch to simple editor
        if (tinymce.get('tinymce-content')) {
            tinymce.get('tinymce-content').destroy();
        }
        // Convert to simple textarea
        const content = document.getElementById('tinymce-content').value;
        const textarea = document.createElement('textarea');
        textarea.id = 'tinymce-content';
        textarea.name = 'content';
        textarea.rows = 12;
        textarea.className = 'block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
        textarea.placeholder = 'Introduceți conținutul text al emailului...';
        textarea.value = content;

        const container = document.getElementById('tinymce-content').parentElement;
        container.replaceChild(textarea, document.getElementById('tinymce-content'));

        // Set is_html to false for simple editor
        document.getElementById('is_html').value = '0';
    } else {
        // Switch to TinyMCE
        if (tinymce.get('tinymce-content')) {
            tinymce.get('tinymce-content').destroy();
        }
        // Convert to TinyMCE textarea
        const content = document.getElementById('tinymce-content').value;
        const textarea = document.createElement('textarea');
        textarea.id = 'tinymce-content';
        textarea.name = 'content';
        textarea.rows = 12;
        textarea.className = 'block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
        textarea.placeholder = 'Introduceți conținutul HTML al emailului...';
        textarea.value = content;

        const container = document.getElementById('tinymce-content').parentElement;
        container.replaceChild(textarea, document.getElementById('tinymce-content'));

        // Initialize TinyMCE
        initTinyMCEEditor();

        // Set is_html to true for TinyMCE editor
        document.getElementById('is_html').value = '1';
    }
};

// Inițializăm editorul la încărcarea paginii
document.addEventListener('DOMContentLoaded', function () {
    initTinyMCEEditor();
});
