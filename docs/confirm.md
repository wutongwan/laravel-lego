# Confirm

删除数据时需要用户进行一次 confirm , That's it !

### 示例

```php
$suite = Suite::findOrFail($id);
return Lego::confirm(
	"确认删除公寓 {$suite->address} ?",
	function () {
	    $suite->delete();
	    return redirect(...);
	}
)
```

### 若需要处理 非确认（取消） 操作

```php
return Lego::confirm(
	"确认删除公寓 {$suite->address} ?",
	function ($sure) {
		if ($sure) {
		    $suite->delete();
		    return redirect(...);
		} else {
			// do something.
		}
	}
)
```

### 强制等候 3 秒后才可确认

```php
return Lego::confirm(
	"确认删除公寓 {$suite->address} ?",
	function () {
	    $suite->delete();
	    return redirect(...);
	},
	3
)
```
