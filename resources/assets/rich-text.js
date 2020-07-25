// tinymce
import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default'; // Default icons are required for TinyMCE 5.3 or above
import 'tinymce/themes/silver'; // A theme is also required

const initTinyMce = function (selector) {
    tinymce.init({
        selector,
        menubar: false,
    })
}

export default initTinyMce
