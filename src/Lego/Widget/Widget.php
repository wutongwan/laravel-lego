<?php namespace Lego\Widget;

use Lego\Helper\InitializeHelper;
use Lego\Helper\MessageHelper;
use Lego\Helper\StringRenderHelper;
use Lego\Source\Source;

/**
 * Lego中所有大型控件的基类
 */
abstract class Widget
{
    use MessageHelper;
    use InitializeHelper;
    use StringRenderHelper;

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

        // 初始化
        $this->triggerInitialize();
    }

    protected function source() : Source
    {
        return $this->source;
    }
}