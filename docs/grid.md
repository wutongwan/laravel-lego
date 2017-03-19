# Grid


## Basic

```php
$grid = \Lego::grid($query);

$grid->add('id', 'ID'));
$grid->add('summary', '摘要');
$grid->add('billing_date|date', '账单日');
$grid->add('fen|fen2yuan', '金额（元）');
$grid->add('receiver.name', '接收人');
$grid->add('paid_at', '付款时间');
$grid->add('created_at', '创建时间');
$grid->paginate(100)->orderBy('id', true); // order by id desc

return $grid->view('view-file', compact('filter', 'grid'));
```

### insert to special location

```php
$grid->after('id')->add('column', 'Description');
```

### remove

```php
$grid->remove('id');
$grid->remove('paid_at', 'created_at');
$grid->remove(['paid_at', 'created_at']);
```

### set default value

```php
$grid->add('column', 'Description')->default('default value');
```

### add button

```php
$grid->addLeftTopButton('new', route('...'));
```

## Pipe

### Basic

```php
$grid->add('name|trim|strip', 'Name');
```

```php
$grid = Lego::grid(City::class);

$grid->add('name|trim', 'Name')

    ->pipe('strip')

    ->pipe(function ($name) {
        return do_some_thing($name);
    })

    ->pipe(function ($name, City $city) {
        /**
        * $city is the model of current row
        */
        return $name . '(' . $city->name .')';
    })

    ->pipe(function ($name, City $city, Cell $cell) {
        /**
        * $cell is instanceof \Lego\Widget\Grid\Cell
        * 
        * $cell->name() === 'name'; // Important: NO pipe name
        * $cell->description() === 'Name';
        * $cell->getOriginalValue();
        */
        
        return $name;
    });
```

### Available pipes

- trim

    ```php
    $grid->add('name|trim', 'Name');
    ```

- strip, remove html tags

	```php
	$grid->add('name|strip', 'Name');
	```

- date, convert to date string

    ```php
    $grid->add('published_at|date', 'Published Date');
    // eg: 2017-01-01 12:00:01 => 2017-01-01
    ```

- time, convert to time string

    ```php
    $grid->add('updated_at|time', 'Last Moidify Time');
    // eg: 2017-01-01 12:00:01 => 12:00:01
    ```

### Self-Defined Pipe

```php
lego_register(
    GridCellPipe::class,
    function ($name, Model $model, Cell $cell) {
        return $model->getAttribute($cell->name() . '_en')
    }, 
    'trans2en'
)
```

```php
$grid->add('name|trans2en', 'Name');
```

## Export as Excel (.xls)

### Simple

```php
$grid->export('filename');
```

> **Notice：** Lego 导出功能依赖 [Laravel-Excel](https://github.com/Maatwebsite/Laravel-Excel)，所以需要将下面类注册到 `config/app.php` 的 `providers` 中：
> 
> ```php
> Maatwebsite\Excel\ExcelServiceProvider::class
> ```

### Exporting callback

```php
$grid->export('filename', function (Grid $grid) {
    $grid->paginate(1000); // export more
})
```

## 批处理操作

### 一键批处理

```php
$grid->addBatch('批量删除')
    ->each(function (Advance $advance) {
        $advance->delete();
    });
```

```php
$grid->addBatch('汇总')
    ->handle(function (Collection $advances) {
        return Lego::message('共 ' . $advances->sum('amount') . ' 元');
    });
```

### 带确认信息的批处理
```php
$grid->addBatch('批量删除')
    ->message('确认删除？')
    ->each(function (Advance $advance) {
        $advance->delete();
    });
```

### 带动态确认信息的批处理
```php
$grid->addBatch('批量删除')
    ->message(function (Collection $advances) {
        return "确认删除 {$advances->count()} 条记录？"
    })
    ->each(function (Advance $advance) {
        $advance->delete();
    });
```

### 带表单的批处理

```php
$grid->addBatch('变更状态')
    ->form(function (Form $form) {
        $form->addSelect('status')->values('已付款', '作废')->required();
    })
    ->action(function (Advance $advance, Form $form) {
        $advance->status = $form->field('status')->getNewValue();
        $advance->save();
    });
```


