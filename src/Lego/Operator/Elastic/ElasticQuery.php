<?php

namespace Lego\Operator\Elastic;

use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lego\Foundation\Exceptions\NotSupportedException;
use Lego\Operator\Query;
use Lego\Operator\SuggestResult;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\WildcardQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;

/**
 * Class ElasticQuery
 * @package Lego\Operator\ES
 * @property Search $data for ide autocomplete
 */
class ElasticQuery extends Query
{
    const WILDCARD_LIMIT = 50;

    public static function parse($client)
    {
        if ($client instanceof ElasticClient) {
            $query = new static(new Search());
            $query->setClient($client);
            return $query;
        }

        return false;
    }

    /**
     * @var ElasticClient
     */
    protected $client;

    /**
     * @param ElasticClient $client
     */
    public function setClient(ElasticClient $client)
    {
        $this->client = $client;
    }

    public function whereEquals($attribute, $value)
    {
        $query = new TermQuery($attribute, $value);
        $this->addBoolQuery()->add($query);
        return $this;
    }

    public function whereIn($attribute, array $values)
    {
        $query = new TermsQuery($attribute, array_values($values));
        $this->addBoolQuery()->add($query);
        return $this;
    }

    public function whereGt($attribute, $value, bool $equals = false)
    {
        return $this->whereRange($attribute, $equals ? '>=' : '>', $value);
    }

    public function whereLt($attribute, $value, bool $equals = false)
    {
        return $this->whereRange($attribute, $equals ? '<=' : '<', $value);
    }

    protected function whereRange($field, $operator, $value)
    {
        list($value, $timezone) = self::tryFormatDatetimeForRange($value);

        $mapping = [
            '>' => 'gt',
            '>=' => 'gte',
            '<' => 'lt',
            '<=' => 'lte',
        ];

        if (!isset($mapping[$operator])) {
            throw new NotSupportedException('unsupported range operator: ' . $operator);
        }

        $range = [$mapping[$operator] => $value];
        if ($timezone) {
            $range['time_zone'] = date_default_timezone_get();
        }

        $rangeQuery = new RangeQuery($field, $range);
        $this->addBoolQuery()->add($rangeQuery);

        return $this;
    }

    /**
     * 尝试将带时间的日期转换为 ES 可识别的格式
     *
     * 1、如果 input 是 Carbon 对象：格式化为 iso8601 字符串（带时区）
     * 2、如果 input 非字符串：不做任何处理
     * 3、如果 input 是符合格式 `Y-m-d` 的日期字符串：不做任何处理，并返回 time_zone 参数
     * 4、如果 input 是符合格式 `Y-m-d H:i:s` 的时间字符串：格式化为 iso8601 字符串（带时区）
     *
     * @param $input
     *
     * @return array
     */
    protected static function tryFormatDatetimeForRange($input)
    {
        if ($input instanceof DateTime) {
            return [$input->format(DateTime::ATOM), true];
        }

        if (!is_string($input)) {
            return [$input, false];
        }

        // iso8601 格式，es 可以直接识别，并且带时区
        if (DateTime::createFromFormat(DateTime::ATOM, $input) !== false) {
            return [$input, true];
        }

        // 检查是否日期字符串: Y-m-d, 是则添加 time_zone 参数
        // es 在针对日期做 range 时会对值进行四舍五入，例如值为 2020-03-08 时:
        //     gt:  '2020-03-08T23:59:59.999'
        //     gte: '2020-03-08T00:00:00.000'
        //     lt:  '2020-03-08T00:00:00.000'
        //     lte: '2020-03-08T23:59:59.999'
        // 详细文档见: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html#range-query-date-math-rounding
        // 另外默认四舍五入的值是 UTC 时区，所以需要添加 time_zone 参数
        if ($date = DateTime::createFromFormat('Y-m-d', $input)) {
            // 上面使用 Y-m-d 解析日期时会兼容 2020-3-20 格式，但 es 只支持 2020-03-20 这种格式，
            // 所以需要重新进行 format，感觉这是个 PHP 的坑
            return [$date->format('Y-m-d'), true];
        }

        // 兼容时间的日期字符串
        if ($datetime = DateTime::createFromFormat('Y-m-d H:i:s', $input)) {
            return [$datetime->format(DateTime::ATOM), true];
        }

        return [$input, false];
    }

    public function whereContains($attribute, string $value)
    {
        return $this->whereWildcard($attribute, "*" . trim($value, '*') . "*");
    }

    public function whereStartsWith($attribute, string $value)
    {
        $query = new PrefixQuery($attribute, $value);
        $this->addBoolQuery()->add($query);
        return $this;
    }

    public function whereEndsWith($attribute, string $value)
    {
        return $this->whereWildcard($attribute, "*" . trim($value, '*'));
    }

    public function whereWildcard($field, $value)
    {
        $value = Str::limit($value, static::WILDCARD_LIMIT);
        $query = new WildcardQuery($field, $value);
        $this->addBoolQuery()->add($query);
        return $this;
    }

    public function whereBetween($attribute, $min, $max)
    {
        // 判断范围值是否是日期，是否需要添加时区参数
        $timezone = false;
        if (is_empty_string($min)) {
            $min = null;
        } else {
            list($min, $timezone1) = self::tryFormatDatetimeForRange($min);
            $timezone = $timezone || $timezone1;
        }
        if (is_empty_string($max)) {
            $max = null;
        } else {
            list($max, $timezone2) = self::tryFormatDatetimeForRange($max);
            $timezone = $timezone || $timezone2;
        }

        $range = [
            RangeQuery::GTE => $min,
            RangeQuery::LTE => $max,
        ];

        if ($timezone) {
            $range['time_zone'] = date_default_timezone_get();
        }

        $rangeQuery = new RangeQuery($attribute, $range);
        $this->addBoolQuery()->add($rangeQuery);

        return $this;
    }

    public function whereScope($scope, $value)
    {
    }

    protected function addBoolQuery($type = BoolQuery::MUST): BoolQuery
    {
        $boolQuery = new BoolQuery();
        $this->data->addQuery($boolQuery, $type);
        return $boolQuery;
    }

    public function suggest($attribute, string $keyword, string $valueColumn = null, int $limit = 20): SuggestResult
    {
        return new SuggestResult([]);
    }

    public function limit($limit)
    {
        $this->data->setSize($limit);
        return $this;
    }

    public function orderBy($attribute, bool $desc = false)
    {
        $sort = new FieldSort($attribute, $desc ? FieldSort::DESC : FieldSort::ASC);
        $this->data->addSort($sort);
        return $this;
    }

    protected function createLengthAwarePaginator($perPage, $columns, $pageName, $page)
    {
        $this->limit($perPage);
        $this->data->setFrom($perPage * ($page - 1));
        $results = $this->data->setStoredFields($columns);
        $collection = $this->convertHitsToCollection($results['hits']['hits'] ?? []);
        $total = $results['hits']['total'] ?? 0;
        return new LengthAwarePaginator($collection, $total, $perPage, $page, ['pageName' => $pageName]);
    }

    protected function createLengthNotAwarePaginator($perPage, $columns, $pageName, $page)
    {
        return $this->createLengthAwarePaginator($perPage, $columns, $pageName, $page);
    }

    protected function search()
    {
        $params = [
            'index' => $this->client->getIndex(),
            'type' => $this->client->getType(),
            'body' => $this->data->toArray(),
        ];

        return $this->client->connection()->search($params);
    }

    protected function select(array $columns)
    {
        $results = $this->data->setStoredFields($columns);
        $hits = $results['hits']['hits'] ?? [];
        return $this->convertHitsToCollection($hits);
    }

    protected function convertHitsToCollection(array $hits)
    {
        $collection = new Collection();

        foreach ($hits as $result) {
            // 未指定 fields
            if (isset($result['_source'])) {
                $collection->push($result['_source']);
                continue;
            }

            // 指定 fields
            $item = [];
            foreach ($result['fields'] as $fieldName => $values) {
                $item[$fieldName] = $values[0] ?? null;
            }
            $collection->push($item);
        }
        return $collection;
    }
}
