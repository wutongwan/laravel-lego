<?php

namespace Lego\Set\Grid;

use Illuminate\Support\Arr;
use Lego\Foundation\FieldName;

class Cell
{
    /**
     * @var FieldName
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $sortAble;

    public function __construct(FieldName $name, string $description, bool $sortAble = false)
    {
        $this->name = $name;
        $this->description = $description;
        $this->sortAble = $sortAble;
    }

    /**
     * @return FieldName
     */
    public function getName(): FieldName
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isSortAble(): bool
    {
        return $this->sortAble;
    }

    public function render($record)
    {
        $value = data_get($record, $this->name->getQualifiedColumnName());

        // 处理 JSON  字段
        if ($value && $this->name->getJsonPath()) {
            if (is_string($value)) {
                if (!$array = \json_decode($value, true)) {
                    throw new \InvalidArgumentException('Invalid json value: ' . $this->name->getQualifiedColumnName());
                }
                return Arr::get($array, $this->name->getJsonPath());
            } else {
                return data_get($value, $this->name->getJsonPath());
            }
        }

        return $value;
    }

}
