<?= '<?' ?>

namespace Lego\Widget\Plugin{
    exit("This file should not be included, only analyzed by your IDE");

    /**
    * Field 相关逻辑
    * ** Magic Add **
<? foreach ($fields as $name => $class) {?>
    * @method \<?= $class ?> add<?= $name ?>(string $fieldName, $fieldDescription)
<? } ?>
    */
    trait FieldPlugin {
    }
}