/**
 * Group with Condition
 */

function LegoConditionGroup(form, field, operator, expected, targetField) {
    var that = this;

    var $form = $(form);
    var $hideZone = $('#lego-hide');
    var fieldSelector = '[name=' + field + ']';
    var targetFieldSelector = '[name=' + targetField + ']';
    var placeholderId = 'lego-condition-group-field-placeholder-' + targetField;
    var containerSelector = '.lego-field-container';

    this.watch = function () {
        this.__check();
        $form.find(fieldSelector).on('change', function () {
            that.__check();
        })
    };

    this.__check = function () {
        var $field = $form.find(fieldSelector);
        if (that.__compare($field.val())) {
            var $container = $hideZone.find(targetFieldSelector).closest(containerSelector);
            if ($container.length === 0) {
                return;
            }
            var $placeholder = $('#' + placeholderId);
            $placeholder.after($container);
            $placeholder.remove();
        } else {
            var $target = $form.find(targetFieldSelector).closest(containerSelector);
            $target.after($('<span/>', {id: placeholderId, class: 'hide'}));
            $target.appendTo($hideZone);
        }
    };

    this.__compare = function (actual) {
        switch (operator) {
            case '=':
            case '==':
            case '===':
                return actual === expected;
            case '!=':
            case '!==':
                return actual !== expected;
            case '>':
                return actual > expected;
            case '>=':
                return actual >= expected;
            case '<':
                return actual < expected;
            case '<=':
                return actual <= expected;
            case 'in':
                return expected.indexOf(actual) !== -1;
            default:
                return false;
        }
    }
}


