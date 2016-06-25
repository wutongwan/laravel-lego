<?php namespace Lego\Source;

interface Source
{
    public function __construct($data);

    public function set($key, $value);

    public function get($key, $default = null);
}