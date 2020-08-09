import initGridBatch from "./grid-batch"
import initCascadeSelect from "./field-cascade-select";
import initConditionGroup from "./form-condition-group";
import initDatetimePicker from "./field-datatimepicker";
import {initSelect2, initSelect2Autocomplete} from './field-select2'
import {initButtonCountdown, initButtonPreventRepeat} from './button'
import {initFilterInlineStyle} from './filter'

import './style.css'


export default function registerJqueryListeners(lego) {
    // field: tinymce
    if (document.getElementsByClassName('lego-field-tinymce').length > 0) {
        import(/* webpackChunkName: "ui-bootstrap-jquery-tinymce" */ './field-richtext-tinymce')
            .then(({default: initTinymce}) => initTinymce('.lego-field-tinymce'))
    }

    // filter inline style
    document.querySelectorAll('.lego-filter-style-inline').forEach(el => initFilterInlineStyle(el));

    // grid-batch
    document.querySelectorAll('.lego-grid-batch').forEach(el => initGridBatch(el))

    // condition group
    document.querySelectorAll('.lego-condition-group').forEach(el => initConditionGroup(el))

    // cascade select
    document.querySelectorAll('[data-lego-cascade-select]').forEach(el => initCascadeSelect(el))

    // field: datetime
    document.querySelectorAll('.lego-field-datetime').forEach(el => initDatetimePicker(el))

    // field: select2
    document.querySelectorAll('.lego-field-select2').forEach(el => initSelect2(el))

    // field: auto complete
    document.querySelectorAll('.lego-field-autocomplete').forEach(el => initSelect2Autocomplete(el))

    // 防止按钮重复点击
    document.querySelectorAll('.lego-button-prevent-repeat').forEach(btn => initButtonPreventRepeat(btn))

    // 按钮倒计时后才可以点击
    document.querySelectorAll('[data-lego-button-delay]').forEach(btn => initButtonCountdown(btn))
}
