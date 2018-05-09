# Filter 筛选器

## Example

```php
$filter = \Lego::filter(new Blog);
$filter->addText('title', 'Title');
$filter->addDaterange('created_at', 'Created At');
```


## OutgoingQuery Scope

```php
$filter->addSelect('custom')->values('hot', 'normal')
	->scope('hotOrNormal') // Laravel OutgoingQuery Scope, call `Blog::scopeHotOrNormal($query, $value)`
	// 注意下面的第一个参数为 Lego 中内置的 OutgoingQuery 接口类，并非 Laravel 中的 QueryBuilder
	->scope(function (OutgoingQuery $query, $value) {
	    return $value === 'hot'
	        ? $query->whereGt('pv', 1000)
	        : $query->whereLte('pv', 1000)
	})
```


使用 Field name 作为 Scope name

```php
$filter->addSelect('hotOrNormal')->scope()
```
