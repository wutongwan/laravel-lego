<?php namespace Lego\Tests\Tools;

class FakeMobileDetect extends \Mobile_Detect
{
    protected static $mocks = [];

    public static function mockIsMobile($condition = true)
    {
        static::$mocks['is-mobile'] = $condition;
    }

    public static function mockIs($key, $condition = true)
    {
        static::$mocks[$key] = $condition;
    }

    public static function forgetMocks()
    {
        static::$mocks = [];
    }

    public function isMobile($userAgent = null, $httpHeaders = null)
    {
        if (isset(static::$mocks['is-mobile'])) {
            return static::$mocks['is-mobile'];
        }

        return parent::isMobile($userAgent, $httpHeaders);
    }

    public function is($key, $userAgent = null, $httpHeaders = null)
    {
        if (isset(static::$mocks[$key])) {
            return static::$mocks[$key];
        }

        return parent::is($key, $userAgent, $httpHeaders);
    }
}
