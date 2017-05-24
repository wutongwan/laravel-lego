const {mix} = require('laravel-mix');
const fs = require('fs');

// global config.
mix.setPublicPath('public');
mix.version();

let vendors = new Map([
    // Bootstrap
    ['bootstrap/dist/js/bootstrap.min.js', 'bootstrap/bootstrap.js'],
    ['bootstrap/dist/css/bootstrap.min.css', 'bootstrap/bootstrap.css'],
    ['bootstrap/dist/fonts/', 'bootstrap/fonts'],

    // jQuery
    ['jquery/dist/jquery.min.js', 'jquery.js'],

    // Vue
    ['vue/dist/vue.min.js', 'vue.js'],
    ['vue-resource/dist/vue-resource.min.js', 'vue-resource.js'],

    // select2
    ['select2/dist/js/select2.full.js', 'select2'],
    ['select2/dist/js/i18n', 'select2/i18n'],
    ['select2-bootstrap-theme/dist/select2-bootstrap.css', 'select2/theme.css'],

    // bootstrap datetime picker
    ['bootstrap-datetime-picker/js/bootstrap-datetimepicker.js', 'time-picker/picker.js'],
    ['bootstrap-datetime-picker/js/locales', 'time-picker/locales'],
    ['bootstrap-datetime-picker/css/bootstrap-datetimepicker.css', 'time-picker/theme.css'],

    // iCheck
    ['icheck/icheck.min.js', 'check/check.js'],
    ['icheck/skins/square/blue.css', 'check/skin.css'],
]);

let handle = function (vendors, sourceDirectory, targetDirectory) {
    for (let [from, to] of vendors) {
        let source = sourceDirectory + '/' + from;
        let target = targetDirectory + '/' + to;

        if (fs.lstatSync(source).isDirectory()) {
            mix.copy(source, target);
        } else if (source.endsWith('.js')) {
            mix.js(source, target);
        } else if (source.endsWith('.css')) {
            mix.styles(source, target);
        }
    }
};

handle(vendors, 'node_modules', 'public');

mix.autoload({
    jquery: ['$', 'window.jQuery']
});


mix.babel('resources/assets/field/cascade-select.js', 'public/field/cascade-select.js');
mix.babel('resources/assets/field/condition-group.js', 'public/field/condition-group.js');
