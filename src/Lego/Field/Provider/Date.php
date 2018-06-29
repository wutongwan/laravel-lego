<?php

namespace Lego\Field\Provider;

class Date extends Datetime
{
    protected $format = 'Y-m-d';

    protected $inputType = 'date';

    protected $minView = 'month';
}
