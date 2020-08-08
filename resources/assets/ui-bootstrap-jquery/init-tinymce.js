// Import TinyMCE
import tinymce from 'tinymce/tinymce';

// Default icons are required for TinyMCE 5.3 or above
import 'tinymce/icons/default';

// A theme is also required
import 'tinymce/themes/silver';

// Any plugins you want to use has to be imported
import 'tinymce/plugins/paste';
import 'tinymce/plugins/link';

import 'tinymce/skins/ui/oxide/content.min.css'
import 'tinymce/skins/ui/oxide/skin.min.css'

require.context(
    'file-loader?name=[path][name].[ext]&context=node_modules/tinymce!tinymce/skins',
    true,
    /.*/
);

const initTinyMce = function (selector) {
    // Initialize the app
    tinymce.init({
        selector,
        menubar: false,
        plugins: ['paste', 'link']
    });
}

export default initTinyMce