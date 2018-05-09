# Filter 输出筛选条件


```php

$filter = Lego::outgoingFilter();
$filter->addText('name', 'Name');
$filter->addDateRange('created_at', 'Create Time');
...

/**
 * conditions 数据结构
 *
 *  [
 *      'wheres' => [
 *          [
 *              'attribute' => 'name',
 *              'operator' => 'contains', // eg: =, >, >=, contains, in, between etc.
 *              'value' => '...',
 *          ],
 *          [
 *              'attribute' => 'created_at',
 *              'operator' => 'between',
 *              'value' => ['2018-01-01', '2018-12-31'],
 *          ],
 *          ...
 *      ],
 *      'limit' => 100,
 *      'orders' => [
 *          ['id', 'desc'],
 *          ['...', 'asc'],
 *          ...
 *      ],
 *  ]
 *
 */
 
$filter->processOnce();
$conditions = $filter->getQuery()->toArray();

$rows = parseAndCallSomeAPI($conditions);
$gird = Lego::grid($rows);
...

```


## `operator` 列表

- `=`
- `>`
- `>=`
- `<`
- `<=`
- `in`
- `between`
- `contains`
- `contains:starts_with`
- `contains:ends_with`
- `scope`






