<?php namespace Lego\Widget;

use Lego\Helper\InitializeHelper;
use Lego\Helper\MessageHelper;
use Lego\Helper\StringRenderHelper;
use Lego\Helper\TraitInitializeHelper;
use Lego\Source\Source;

/**
 * Lego中所有大型控件的基类
 */
abstract class Widget
{
    use MessageHelper;
    use InitializeHelper;
    use StringRenderHelper;
    use TraitInitializeHelper;

    // Plugins
    use Plugin\FieldPlugin;
    use Plugin\GroupPlugin;

    /**
     * 数据源
     * @var Source $source
     */
    private $source;

    public function __construct($data)
    {
        $this->source = lego_source($data);

        // 初始化控件
        $this->initialize();

        // 初始化 traits (plugins)
        $this->initializeTraits();
    }

    protected function source() : Source
    {
        return $this->source;
    }
}