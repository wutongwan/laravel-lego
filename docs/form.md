# Lego Form

Lego 中的表单组件

## example code

```php

$blog = Blog::find($blogId);

$form = Lego::form($blog);
$form->addText('title', 'Blog Title')->required();
$form->addTextarea('content')->required();
$form->addAutoComplete('author.name', 'Select Author')->required();
$form->addDatetime('created_at', 'Created At')->required();

return $form->view('layout', compact('form'));
```

## 自定义提交按钮的文本

```php
$form->submitText('完成');

// 用于 Filter
$form->resetText('清空查询');
```

## 自定义表单提交后的处理逻辑

```php
$form->onSubmit(function (Form $form) {
	$form->field('xxx')->getNewValue();
	// ...
});
```
