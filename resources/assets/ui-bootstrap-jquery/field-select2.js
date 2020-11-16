import 'select2'
import 'select2/dist/css/select2.css'
import 'select2-bootstrap-theme/dist/select2-bootstrap.min.css'

function loadSelect2Locale(locale, callback) {
    if (locale !== 'en') {
        import(
            /* webpackChunkName: "i18n/select2/[request]" */
            `select2/dist/js/i18n/${locale}.js`
            ).then(callback)
    } else {
        callback()
    }
}

export function initSelect2(field) {
    const $field = jQuery(field)
    loadSelect2Locale($field.data('language'), () => {
        $field.select2({
            placeholder: $field.data('placeholder'),
            theme: "bootstrap",
            width: "100%",
            language: $field.data('language'),
            allowClear: $field.data('allow-clear'),
        })
    })
}

export function initSelect2Autocomplete(field) {
    const $field = jQuery(field)

    // 监听事件，修改文本输入框
    const textInput = document.getElementsByName($field.data('text-input-name'))
    $field.on('select2:select', (event) => textInput.value = event.params.data.text)
    $field.on('select2:unselect', () => textInput.value = null)

    loadSelect2Locale($field.data('language'), () => {
        // 构建 select2 组件
        $field.select2({
            placeholder: $field.data('placeholder'),
            theme: "bootstrap",
            width: "100%",
            language: $field.data('language'),
            allowClear: $field.data('allow-clear'),
            minimumInputLength: $field.data('setMinInputLength-input-length'),
            ajax: {
                url: decodeURIComponent($field.data('url')),
                dataType: 'json',
                delay: 700,
                cache: true,
                data: function (params) {
                    return {
                        '__lego_auto_complete': params.term,
                        "page": params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items.map(function (item) {
                            return {
                                id: item.value,
                                text: item.label,
                            }
                        }),
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }
            }
        })
    })
}
