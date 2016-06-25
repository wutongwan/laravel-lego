<?php namespace Lego\Widget;

use Lego\LegoException;
use Lego\Source\Source;
use Lego\Source\ArraySource;
use Lego\Source\EloquentSource;
use Lego\Source\ObjectSource;

use Illuminate\Support\Collection as Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Lego中所有大型控件的基类
 */
abstract class Widget
{
    /**
     * 数据源
     * @var Source $source
     */
    private $source;

    public function __construct($source)
    {
        $first = $source[0] ?? array();

        switch (true) {
            // Eloquent Source
            case $first instanceof Eloquent:
            case $source instanceof EloquentCollection:
                $this->source = new EloquentSource($source);
                break;

            // Array Source
            case is_array($first):
                $this->source = new ArraySource($source);
                break;

            // Object Source
            case is_object($first):
                $this->source = new ObjectSource($source);
                break;

            default:
                throw new LegoException('Illegal $source type');
        }
    }

    protected function getSource() : Source
    {
        return $this->source;
    }
}