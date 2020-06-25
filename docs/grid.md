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

## Responsive view

Responsive view is enabled by default, you can disabled it in config file `lego.php`,  
In addition , you can call `responsive()` method to enable it for current instance.

- Disable Responsive view Globally
```php
[
    'widgets' => [
        'grid' => [
            'responsive' => false,
        ]
    ]
]
```

- Enable responsive view for current `$grid` instance
```php
$grid->responsive();
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

使用者可以继承 `\Lego\Widget\Grid\Pipes` 实现自己的 Pipes ，然后将其注册到 lego 的配置文件的 `widgets.grid.pipes` 数组中，这样在 Grid 就可以使用 `|` 引入 pipe。

下面示例创建了一个用于翻译的 pipes 类，其中所有以 `handle` 开头的成员函数将会被识别为可以直接使用的 pipe

`\Lego\Widget\Grid\Pipes` 有三个成员函数，用于获取管道需要的数据：

- `value()` 输入 `pipe` 的值，例如下面的 `handleUpper`
- `data()` 这一行对应的原始数据，如果数据源是 Laravel Model ，此函数返回值为 Model 实例
- `cell()` 返回 `\Lego\Widget\Grid\Cell` 实例，当前单元格的描述、对应的数据库字段，都可以通过此实例获取


```php
class Pipes extends \Lego\Widget\Grid\Pipes
{
    pubilc function handleUpper()
    {
        return strtoupper($this->value());
    }

    public function handleTrans2en()
    {
        return $this->data()->getAttribute(
            $this->cell()->name() . 'en'
        );
    }
}
```

```php
$grid->add('name|trans2en', 'Name');
```

## Format & Link

```php
$grid->add('id', 'Edit')
    ->format('Edit {}:{address}')
    ->link('https://example.com/edit/{}');
```

same as

```php
$grid->add('id', 'Edit')
    ->pipe(function ($id, $model) {
        return sprintf(
            '<a href="%s" target="_blank">Edit %s:%s</a>',
            'https://example.com/edit/' . $id,
            $id,
            $model->address
        ); 
    });
```

Format 和 Link 中占位符示例：

- `{}` => cell value
- `{id}` => $model->id
- `{related.name}` => $model->related->name

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


> **Tips: 在新 tab、新窗口发起批处理**
>
> ```php
> $grid->addBatch(...)->openInNewTab();
> $grid->addBatch(...)->openInPopup($width = ..., $height = ...);
> $grid->addBatch(...)->resetOpenTarget()
```
