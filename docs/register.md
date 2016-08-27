# Lego注册器

_Code: `Lego/Register/Register.php`_


## 添加注册项

在 `Lego/Register/Data` 中创建对应数据类，示例如下：

```php
class FieldData extends Data
{
    /**
     * 校验注册的数据是否合法, 不合法时抛出异常
     * @param array $data
     */
    protected function validate(array $data = [])
    {
    }

    /**
     * 注册完成后的回调 （可选）
     */
    public function afterRegistered()
    {
    }
    
    // other helper methods
}

```


## 注册

```php

lego_register('field.data', Room::class, [
	'address' => ['description' => '地址'],
	// ..
]);
```


## 使用注册的数据

```php

$data = lego_register('field.data', Room::class);

// $data instanceof \Lego\Register\Data\FieldData
```