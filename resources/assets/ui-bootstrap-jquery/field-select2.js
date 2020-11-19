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
    if (field.getAttribute('data-lego-url')) {
        initSelect2Autocomplete(field)
    } else {
        loadSelect2Locale(field.getAttribute('data-language'), () => jQuery(field).select2())
    }
}

function initSelect2Autocomplete(field) {
    const $field = jQuery(field)

    // 监听事件，修改文本输入框
    const textInput = document.getElementsByName($field.data('lego-text-input-name'))[0]
    $field.on('select2:select', (event) => textInput.value = event.params.data.text)
    $field.on('select2:unselect', () => textInput.value = null)

    loadSelect2Locale($field.data('language'), () => {
        // 构建 select2 组件
        $field.select2({
            ajax: {
                url: decodeURIComponent($field.data('lego-url')),
                dataType: 'json',
                delay: 700,
                cache: true,
                data: function (params) {
                    return {
                        '__lego_auto_complete': params.term,
                        "__lego_auto_complete_page": params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.items,
                        pagination: {
                            more: data.hasMore
                        }
                    };
                }
            }
        })
    })
}
