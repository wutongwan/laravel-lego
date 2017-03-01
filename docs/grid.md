# Grid


## Example

```php
$filter = \Lego::filter($query);
$filter->addText('summary', '摘要');
$filter->addDateRange('billing_date', '账单日');
$filter->addText('receiver.name', '接收人');

$grid = \Lego::grid($filter);
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

## 批处理操作

### 一键批处理

```php
$grid->addBatch('批量删除')
    ->action(function (Advance $advance) {
        $advance->delete();
    });
```

### 带确认信息的批处理
```php
$grid->addBatch('批量删除')
	->message('确认删除？')
    ->action(function (Advance $advance) {
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


