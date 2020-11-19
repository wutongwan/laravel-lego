<?php

namespace Lego\Contracts;

use Lego\ModelAdaptor\QueryAdaptor;

interface HasQueryAdaptor
{
    public function getQueryAdaptor(): QueryAdaptor;
}
