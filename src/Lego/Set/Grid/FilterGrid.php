<?php

namespace Lego\Set\Grid;

use Illuminate\Contracts\View\Factory;
use Lego\ModelAdaptor\ModelAdaptorFactory;
use Lego\Set\Filter\Filter;

class FilterGrid extends Grid
{
    /**
     * @var Filter
     */
    private $filter;

    public function __construct(Factory $view, ModelAdaptorFactory $factory, Filter $filter)
    {
        $this->filter = $filter;

        parent::__construct($view, $factory, $filter->getAdaptor());
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }
}
