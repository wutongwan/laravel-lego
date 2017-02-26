<?php

use Lego\Foundation\Exceptions\LegoException;
use Lego\Register\HighPriorityResponse;
use Lego\LegoRegister;

/**
 * Lego Assert
 * @param $condition
 * @param $description
 * @throws LegoException
 */
function lego_assert($condition, $description)
{
    LegoException::assert($condition, $description);
}

/**
 * Alias to LegoRegister::register
 */
function lego_register($name, $data, $tag = LegoRegister::DEFAULT_TAG)
{
    return LegoRegister::register($name, $data, $tag);
}

function is_empty_string($string)
{
    return strlen(trim($string)) === 0;
}

/**
 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
 */
function lego_response()
{
    /**
     * Check registered global response
     */
    $registeredResponse = HighPriorityResponse::getResponse();
    if (!is_null($registeredResponse)) {
        return $registeredResponse;
    }

    return call_user_func_array('response', func_get_args());
}

/**
 * 按顺序取默认值
 *
 * lego_default($value, $default1, $default2, $default3, ...)
 *
 * @return mixed|null
 */
function lego_default()
{
    foreach (func_get_args() as $value) {
        if (!is_null($val = value($value))) {
            return $val;
        }
    }
    return null;
}
