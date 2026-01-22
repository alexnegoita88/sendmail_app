import './bootstrap';
import './email-editor';
import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';
import newsletter from 'grapesjs-preset-newsletter';
import mjml from 'grapesjs-mjml';

// Make GrapesJS globally available
window.grapesjs = grapesjs;
window.grapesjs.presets = {
    newsletter,
    mjml
};
