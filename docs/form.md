# Lego Form

Lego 中的表单组件

- example code

```php

$blog = Blog::find($blogId);

$form = Lego::form($blog);
$form->addText('title', 'Blog Title')->required();
$form->addAutoComplete('author.name', 'Select Author')->required();
$form->addDatetime('created_at', 'Created At')->required();

$form->addRightTopButton('Delete')
	->action(function () use ($blog) {
		$blog->delete();
		flash('Blog removed.')
		return redirect('/blog-list');
	})

return $form->view('layout', compact('form'));
```