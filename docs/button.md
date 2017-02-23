# Button

按钮，Filter、Grid 和 From 都支持在左上、左下、右上和右下四个方位添加按钮。
 
## Example

```php
$filter->addRightTopButton('分派工单', $url);
```

## 按钮一键操作

```php
$form->addRightTopButton('Delete')
	->action(function () use ($blog) {
		$blog->delete();
		flash('Blog removed.')
		return redirect('/blog-list');
	})
```
