import tinymce from 'tinymce/tinymce';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/icons/default/icons';
import 'tinymce/themes/silver/theme';
import 'tinymce/models/dom/model';
import 'tinymce/plugins/lists';

export function initTinyMCE() {
    tinymce.init({
        selector: '#tinyredactor',
        skin: false,
        content_css: false,
        plugins: ['lists'],
        license_key: 'gpl',
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
    });
}