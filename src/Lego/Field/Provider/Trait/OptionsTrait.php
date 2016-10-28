<?php
//  zhanghuiren@dankegongyu.com

namespace Lego\Field\Provider;


trait OptionsTrait
{
    /**
     * @var array
     */
    protected $options = [];

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 因为 set 比 get 常用的多的多, 所以没前缀表示设置
     * @param array $options
     */
    public function options(array $options = [])
    {
        $this->options = $options;
    }
}
