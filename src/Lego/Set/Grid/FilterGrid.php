<?php

namespace Lego\Set\Grid;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Lego\Foundation\Response\ResponseManager;
use Lego\ModelAdaptor\ModelAdaptorFactory;
use Lego\Set\Filter\Filter;

class FilterGrid extends Grid
{
    /**
     * @var Filter
     */
    private $filter;

    public function __construct(
        Container $container,
        Factory $view,
        ResponseManager $responseManager,
        ModelAdaptorFactory $factory,
        Filter $filter
    ) {
        $this->filter = $filter;
        parent::__construct($container, $view, $responseManager, $factory, $filter);
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }
}
