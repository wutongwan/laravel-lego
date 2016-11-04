<?= '<?' ?>

namespace Lego\Widget\Plugin{
    exit("This file should not be included, only analyzed by your IDE");

    /**
    * Field 相关逻辑
    * ** Magic Add **
<? foreach ($fields as $field) {?>
    * @method \Lego\Field\Provider\<?= $field ?> add<?= $field ?>(string $fieldName, $fieldDescription)
<? } ?>
    */
    trait FieldPlugin {
    }
}