import {createGridBatch} from './grid-batch.coffee'
import {LegoConditionGroup} from './condition-group'

import 'bootstrap-datetime-picker'
import 'bootstrap-datetime-picker/css/bootstrap-datetimepicker.min.css'

import 'select2'
import 'select2/dist/css/select2.css'
import 'select2-bootstrap-theme/dist/select2-bootstrap.min.css'


class LegoAPI {
    constructor() {
        this.data = new Map()
    }

    setData(type, id, value) {
        if (!this.data.has(type)) {
            this.data.set(type, new Map());
        }

        this.data.get(type).set(id, value)
    }

    getData(type, id, defaultValue = null) {
        if (this.data.has(type) && this.data.get(type).has(id)) {
            return this.data.get(type).get(id)
        }
        return defaultValue;
    }

    register() {
        jQuery(document).ready(this.registerJqueryListeners)
    }

    registerJqueryListeners() {
        // 防止按钮重复点击
        jQuery('.lego-button-prevent-repeat').on('click', function () {
            const btn = this;
            setTimeout(function () {
                jQuery(btn).attr('disabled', true).attr('href', 'javascript:;');
            }, 0)
        })

        // grid 批处理功能
        jQuery('.lego-grid-batch-enabled').on('click', function () {
            const id = jQuery(this).attr('id');
            const data = lego.getData('grid-batch', id)
            createGridBatch(id, data['ids'], data['batches'])
        })

        // filter inline style
        jQuery('.lego-filter-style-inline').each(function () {
            const filter = jQuery(this);
            filter.removeClass('form-inline').addClass('form row');
            filter.find('.form-group').each(function () {
                const group = jQuery(this);
                var times = Math.max(group.find('.form-control').length, 1);
                group.addClass('col-sm-' + (4 * times))
                    .addClass('col-md-' + (3 * times))
                    .addClass('col-lg-' + (2 * times));
            });
        })

        // field: auto complete
        jQuery('.lego-field-autocomplete').each(function () {
            lego.__prepareFieldAutocomplete(jQuery(this))
        })

        // field: datetime
        jQuery('.lego-field-datetime').each(function () {
            const field = jQuery(this);
            field.attr('readonly', true)
                .css('background-color', 'white')
                .css('cursor', 'pointer')
                .datetimepicker(
                    JSON.parse(decodeURIComponent(field.data('datetimepicker-options')))
                )
        })

        // field: select2
        jQuery('.lego-field-select2').each(function () {
            const field = jQuery(this);
            field.select2({
                placeholder: field.data('placeholder'),
                theme: "bootstrap",
                width: "100%",
                language: field.data('language'),
                allowClear: field.data('allow-clear'),
            })
        })

        // field: tinymce
        if (document.getElementsByClassName('lego-field-tinymce').length > 0) {
            import(/* webpackChunkName: "./rich-text" */ './rich-text')
                .then(({default: initTinyMce}) => initTinyMce('.lego-field-tinymce'))
        }
    }

    __prepareFieldCascadeSelect(field) {
        // todo 级联控件改造尚未完成
    }

    __prepareFieldAutocomplete(field) {
        // 监听事件，修改文本输入框
        const textInput = jQuery('#' + field.data('text-input-id'))
        field.on('select2:select', (event) => textInput.val(event.params.data.text))
        field.on('select2:unselect', () => textInput.val(null))

        // 构建 select2 组件
        field.select2({
            placeholder: field.data('placeholder'),
            theme: "bootstrap",
            width: "100%",
            language: field.data('language'),
            allowClear: field.data('allow-clear'),
            minimumInputLength: field.data('min-input-length'),
            ajax: {
                url: decodeURIComponent(field.data('url')),
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
    }
}

const lego = new LegoAPI();

window.lego = lego
window.LegoConditionGroup = LegoConditionGroup

export {
    lego,
    LegoConditionGroup,
}
