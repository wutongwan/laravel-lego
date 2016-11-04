# Fields

## Text

text 输入框

- 示例 HTML：

```html
<input type="text" name="xx" id="xx" />
```


- example usage：

```php
$form = Lego::form(new User);
$form->addText('name', 'Your Name')
	->required()
	->unique()
	->extra('select a nick name');
```

## AutoComplete

自动补全输入框

- example usage：

```php
$form = Lego::form(new Book);
$form->addAutoComplete('author.name', 'Author')
	// 自定义补全结果
	->match(function ($arguments) {
		$keyword = 
	
	})
;

```

## Datetime

日期时间输入框

- 目标数据示例：`2016-11-11 11:11:11`
- example usage：

```php
$form = Lego::form(new Book);
$form->addDatetime('checked_at', 'Checked At');
```

## Date

日期输入框

- 目标数据示例：`2016-11-11`
- example usage：

```php
$form = Lego::form(new Human);
$form->addDate('birthday', 'Birthday');
```

## Time

- 目标数据示例：`11:11:11`
- example usage：

```php
$form = Lego::form(new Schedule);
$form->addTime('daily_at', 'Select Time');
```
