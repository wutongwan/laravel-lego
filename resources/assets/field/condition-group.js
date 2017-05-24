/**
 * Group with Condition
 */

class LegoConditionGroup {
    constructor(form, field, operator, expected, targetField) {
        this.operator = operator;
        this.expected = expected;
        this.$form = $(form);
        this.$hideZone = $('#lego-hide');
        this.fieldSelector = '[name=' + field + ']';
        this.targetFieldSelector = '[name=' + targetField + ']';
        this.placeholderId = 'lego-condition-group-field-placeholder-' + targetField;
        this.containerSelector = '.lego-field-container';
    }

    watch() {
        this.__check();
        this.$form.find(this.fieldSelector).on('change', function () {
            that.__check();
        })
    }

    __check() {
        let $field = this.$form.find(this.fieldSelector);
        if (this.__compare($field.val())) {
            let $container = this.$hideZone.find(this.targetFieldSelector).closest(this.containerSelector);
            if ($container.length === 0) {
                return;
            }
            let $placeholder = $('#' + this.placeholderId);
            $placeholder.after($container);
            $placeholder.remove();
        } else {
            let $target = this.$form.find(this.targetFieldSelector).closest(this.containerSelector);
            $target.after($('<span/>', {id: this.placeholderId, class: 'hide'}));
            $target.appendTo(this.$hideZone);
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


