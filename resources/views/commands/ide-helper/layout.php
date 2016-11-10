<?= '<?' ?>

/**
 * Lego IDE Helper file.
 */

<?
/**
 * @var $fields array
 * @var $widgets array
 */
?>

namespace Lego\Widget\Plugin{

    /**
     * Field 相关逻辑
     * ** Magic Add **
<? foreach ($fields as $name => $class) {?>
     * @method \<?= $class ?> add<?= $name ?>(string $fieldName, $fieldDescription = null)
<? } ?>
     *
     * @see \Lego\Widget\Plugin\FieldPlugin
     */
    trait FieldPlugin
    {
    }
}

namespace Lego\Widget {

<? foreach ($widgets as $widget => $detail) {?>
    /**
<? foreach ($detail['methods'] as $method) {?>
     * @method <?= $method['return'] ?> <?= $method['name'] ?>(<?= $method['arguments'] ?>)
<? } ?>
     *
     * @see \<?= $widget . "\n" ?>
     */
    class <?= class_basename($widget) ?>

    {
    }

<? } ?>

}