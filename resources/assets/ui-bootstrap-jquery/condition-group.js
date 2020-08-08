/**
 * Group with Condition
 */

class ConditionGroup {
    constructor(formId, field, operator, expected, targetField) {
        this.formId = formId
        this.field = field
        this.operator = operator
        this.expected = expected

        this.$form = jQuery(`#${formId}`);
        this.$hideZone = jQuery('#lego-hide');
        this.fieldSelector = '[name=' + field + ']';
        this.targetFieldSelector = '[name=' + targetField + ']';
        this.placeholderId = 'lego-condition-group-field-placeholder-' + targetField;
        this.containerSelector = '.lego-field-container';
    }

    watch() {
        this.__check();
        this.$form.find(this.fieldSelector).on('change', () => this.__check())
        console.log('running')
    };

    __check() {
        const that = this
        var $field = this.$form.find(this.fieldSelector);
        if (that.__compare($field.val())) {
            var $container = that.$hideZone.find(that.targetFieldSelector).closest(that.containerSelector);
            if ($container.length === 0) {
                return;
            }
            var $placeholder = jQuery('#' + that.placeholderId);
            $placeholder.after($container);
            $placeholder.remove();
        } else {
            var $target = that.$form.find(that.targetFieldSelector).closest(that.containerSelector);
            $target.after(jQuery('<span/>', {id: that.placeholderId, class: 'hide'}));
            $target.appendTo(that.$hideZone);
        }
    };

    __compare(actual) {
        switch (this.operator) {
            case '=':
            case '==':
            case '===':
                return actual === this.expected;
            case '!=':
            case '!==':
                return actual !== this.expected;
            case '>':
                return actual > this.expected;
            case '>=':
                return actual >= this.expected;
            case '<':
                return actual < this.expected;
            case '<=':
                return actual <= this.expected;
            case 'in':
                return this.expected.indexOf(actual) !== -1;
            default:
                return false;
        }
    }
}

export default ConditionGroup

