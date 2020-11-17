<?php

namespace Lego\Set\Filter;

use Lego\Contracts\Input\FilterInput;
use Lego\Input\Input;
use Lego\ModelAdaptor\QueryAdaptor;
use Lego\Set\Common\InputWrapper;

class FilterInputWrapper extends InputWrapper
{
    use FilterInputWrapperScope;
    use FilterInputWrapperQueryOperator;

    /**
     * @var QueryAdaptor
     */
    private $adaptor;

    /**
     * @var FilterInputHandler
     */
    private $handler;

    /**
     * FilterInputWrapper constructor.
     * @param Input|FilterInput $input
     * @param QueryAdaptor $adaptor
     */
    public function __construct(Input $input, QueryAdaptor $adaptor)
    {
        parent::__construct($input);

        $this->adaptor = $adaptor;

        $handlerClass = $input->filterInputHandler();
        $this->handler = new $handlerClass($input, $this);
    }

    /**
     * @return QueryAdaptor
     */
    public function getAdaptor(): QueryAdaptor
    {
        return $this->adaptor;
    }

    public function handler(): FilterInputHandler
    {
        return $this->handler;
    }
}
