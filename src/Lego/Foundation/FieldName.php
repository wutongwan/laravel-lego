<?php

namespace Lego\Foundation;

class FieldName
{
    /**
     * 原始名称, eg: country.city.json_column$.jsonKey.jsonSubKey
     * @var string
     */
    private $original;

    /**
     * 最终字段名称, eg: json_column
     * @var string
     */
    private $columnName;

    /**
     * 关系路径, eg: country.city
     * @var string
     */
    private $relation = '';

    /**
     * JSON 路径, eg: `a.b.c`  or `[0][1][2]`
     * @var string
     */
    private $jsonPath = '';

    /**
     * pipelines
     *
     * eg: [ ['name' = 'limit', 'args' => [100]], ...  ]
     *
     * @var array
     */
    private $pipelines = [];

    public function __construct(string $name)
    {
        $this->original = $name;
        $this->parse($name);
    }

    private function parse(string $name)
    {
        // 解析管道
        if (str_contains($name, '|')) {
            $pipelines = self::explode('|', $name);
            $name = array_shift($pipelines);
            $this->pipelines = $this->parsePipelines($pipelines);
        }

        // 解析 JSON 路径
        if (str_contains($name, '$.')) {
            if (count($parts = self::explode('$.', $name, 2)) < 2) {
                throw new \InvalidArgumentException("Invalid Field Name: `{$this->original}`");
            }
            list($name, $this->jsonPath) = $parts;
            if (!preg_match('/^[\w+\.]*$/', $this->jsonPath)) {
                throw new \InvalidArgumentException("Invalid Field Name: `{$this->original}`");
            }
        }

        // 解析关系：country.city.json_column => country.city & json_column
        if (str_contains($name, '.')) {
            $relations = self::explode('.', $name);
            $this->columnName = array_pop($relations);
            $this->relation = join('.', $relations);
        } else {
            $this->columnName = $name;
        }
    }

    private function parsePipelines(array $pipelineStrings): array
    {
        $pipelines = [];
        foreach ($pipelineStrings as $string) {
            $args = [];
            if (str_contains($string, ':')) {
                list($name, $argsString) = self::explode(':', $string, 2);
                $args = self::explode(',', $argsString);
            } else {
                $name = $string;
            }
            $pipelines[] = ['name' => $name, 'args' => $args];
        }
        return $pipelines;
    }

    /**
     * 按指定字符拆分字符串，并过滤掉空字符
     * @return string[]
     */
    private static function explode(string $delimiter, string $string, $limit = null)
    {
        return array_filter(
            $limit ? explode($delimiter, $string, $limit) : explode($delimiter, $string),
            'strlen'
        );
    }

    /**
     * @return string
     */
    public function getOriginal(): string
    {
        return $this->original;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getQualifiedColumnName(): string
    {
        return $this->relation ? "{$this->relation}.{$this->columnName}" : $this->columnName;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @return string
     */
    public function getJsonPath(): string
    {
        return $this->jsonPath;
    }

    /**
     * @return array
     */
    public function getPipelines(): array
    {
        return $this->pipelines;
    }
}
