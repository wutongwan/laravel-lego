const {mix} = require('laravel-mix');
const fs = require('fs');

// global config.
mix.setPublicPath('public/vendor');
mix.version();

let paths = new Map([
    // Bootstrap
    ['bootstrap/dist/js/bootstrap.min.js', 'bootstrap'],
    ['bootstrap/dist/css/bootstrap.min.css', 'bootstrap/bootstrap.min.css'],
    ['bootstrap/dist/fonts/', 'bootstrap/fonts'],

    // jQuery
    ['jquery/dist/jquery.min.js', 'jquery'],

    // Vue
    ['vue/dist/vue.min.js', ''],
    ['vue-resource/dist/vue-resource.min.js', ''],

    // select2
    ['select2/dist/css/select2.min.css', 'select2/select2.min.css'],
    ['select2-bootstrap-theme/dist/select2-bootstrap.min.css', 'select2/bootstrap-style.min.css'],
    ['select2/dist/js/select2.full.min.js', 'select2'],
    ['select2/dist/js/select2.full.min.js', 'select2'],
    ['select2/dist/js/i18n', 'select2/i18n'],

    // bootstrap datetime picker
    ['bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js', 'datetime'],
    ['bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js', 'datetime'],
    ['bootstrap-datetime-picker/js/locales', 'datetime/locales'],
    ['bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css', 'datetime/theme.css'],
]);

for (let [from, to] of paths) {
    let source = 'node_modules/' + from;
    let target = 'public/vendor/' + to;

    if (fs.lstatSync(source).isDirectory()) {
        mix.copy(source, target);
    } else if (source.endsWith('.js')) {
        mix.js(source, target);
    } else if (source.endsWith('.css')) {
        mix.styles(source, target);
    }
}
