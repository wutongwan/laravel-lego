import 'bootstrap-datetime-picker'
import 'bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css'


function loadLocale(locale, callback) {
    if (locale !== 'en') {
        import(
            /* webpackChunkName: "i18n/datetimepicker/[request]" */
            `bootstrap-datetime-picker/js/locales/bootstrap-datetimepicker.${locale}.js`
            ).then(callback)
    } else {
        callback()
    }
}


function initDatetimePicker(field) {
    const $field = jQuery(field)
    const options = JSON.parse(decodeURIComponent($field.data('datetimepicker-options')))
    options['format'] = $field.data('format')
    loadLocale(options['language'], () => {
        $field.attr('readonly', true)
            .css('background-color', 'white')
            .css('cursor', 'pointer')
            .datetimepicker(options)
    })
}

export default initDatetimePicker
