<?= '<?' ?>

/**
 * Lego IDE Helper file.
 */

<?php
/**
 * @var $fields array
 * @var $widgets array
 */
?>

namespace Lego\Widget\Concerns {

    /**
     * Field 相关逻辑
     * ** Magic Add **
<?php foreach ($fields as $name => $class) { ?>
     * @method \<?= $class ?> add<?= $name ?>(string $fieldName, $fieldDescription = null)
<?php } ?>
     *
     * @see \<?= \Lego\Widget\Concerns\HasFields::class ?>

     */
    trait <?= class_basename(\Lego\Widget\Concerns\HasFields::class) ?>

    {
    }
}

namespace Lego\Widget {

<?php foreach ($widgets as $widget => $detail) {?>
    /**
<?php foreach ($detail['methods'] as $method) {?>
     * @method <?= $method['return'] ?> <?= $method['name'] ?>(<?= $method['arguments'] ?>)
<?php } ?>
     *
     * @see \<?= $widget . "\n" ?>
     */
    class <?= class_basename($widget) ?>

    {
    }

<?php } ?>

}
