<?php

namespace Lego\Foundation\Message;

class Message
{
    const INFO = 'info';
    const ERROR = 'error';

    /**
     * @var string
     */
    private $level;

    /**
     * @var string
     */
    private $content;

    public static function info(string $content)
    {
        return new self(self::INFO, $content);
    }

    public static function error(string $content)
    {
        return new self(self::ERROR, $content);
    }

    private function __construct(string $level, string $content)
    {
        $this->level = $level;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    public function isInfo()
    {
        return self::INFO === $this->level;
    }

    public function isError()
    {
        return self::ERROR === $this->level;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
