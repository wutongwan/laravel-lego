<?php

namespace Lego\Foundation;

class FieldName
{
    /**
     * 原始名称, eg: country.city.json_column$.jsonKey.jsonSubKey,.column2|pipe1|pipe2
     * @var string
     */
    private $original;

    /**
     * 最终字段名称, eg: json_column
     * @var string
     */
    private $column;

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
            $this->column = array_pop($relations);
            $this->relation = join('.', $relations);
        } else {
            $this->column = $name;
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

    /**
     * 获取关系的深度
     *
     * eg: country.city => 2
     * eg: country => 1
     *
     * @return int
     */
    public function getRelationDepth(): int
    {
        return $this->relation ? (substr_count($this->relation, '.') + 1) : 0;
    }

    /**
     * 获取关系（返回数组）
     *
     * eg: country.city => [country, city]
     *
     * @return string[]
     */
    public function getRelationList()
    {
        return $this->relation ? explode('.', $this->relation) : [];
    }

    public function getQualifiedColumnName(): string
    {
        return $this->relation ? "{$this->relation}.{$this->column}" : $this->column;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
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

    public function clone(string $column, string $jsonPath = '', array $pipelines = []): FieldName
    {
        $new = clone $this;
        $new->column = $column;
        $new->jsonPath = $jsonPath;
        $new->pipelines = $pipelines;
        $new->original = $new->getQualifiedColumnName()
            . ($jsonPath ? "$.{$jsonPath}" : '')
            . ($pipelines ? ('|' . join('|', $pipelines)) : '');

        return $new;
    }
}
