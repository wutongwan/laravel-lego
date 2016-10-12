## Laravel-Lego

[![Build Status](https://travis-ci.org/wutongwan/laravel-lego.svg?branch=master)](https://travis-ci.org/wutongwan/laravel-lego)
[![Latest Stable Version](https://poser.pugx.org/wutongwan/lego/version.png)](https://packagist.org/packages/wutongwan/lego)
[![Total Downloads](https://poser.pugx.org/wutongwan/lego/d/total.png)](https://packagist.org/packages/wutongwan/lego)

* * *

## Example

```php
$form = Lego::form(new User());

$form->addText('number', '编号')->required()->rule('numeric')->unique();
$form->addText('email', '邮箱')->required()->rule('email')->unique();

return $form->view('layout', ['form' => $form]);
```

![image](http://ww1.sinaimg.cn/large/801b780agw1f8pjbovte0j20n80h4jrz.jpg)


## 系统需求：
  - php >= 7.0 (短时间很多人肯定还没用上 PHP7, but who cares ? 一切向前看！)
  - Laravel >= 5.2

## 安装

1、使用 Composer 添加依赖

```bash
composer require wutongwan/lego
```

2、添加 Service Provider ，将下面的内容添加到 `config/app.php` 的 `providers` 数组中

```php
\Lego\LegoServiceProvider::class,
```

3、发布项目相关文件

```bash
php artisan vendor:publish
```

## 文档

见 [./docs](./docs/README.md)

## 当前版本在以下环境中开发并维护

- Mac、Ubuntu

* * *

## 起因

用了快一年的[zofe/rapyd-laravel](https://github.com/zofe/rapyd-laravel),整体觉得非常好用,但内部确实不少代码年久失修,比较脏，有的组件也不适合中国的情况（比如google map）。

所以我们几个小伙伴想了很久，还是决定重新造个轮子，参考rapyd的思路,做一个我们自己用着更顺手的脚手架库。

起名Lego,也是向乐高致敬,能用简单的Block搭建摩天大厦是我们的梦想。

现在整体代码还是非常初级的版本,有兴趣的朋友欢迎关注以及和我们讨论,但由于整个项目是以提高团队自身效率为最优先前提的,可能我们会独断一些,不会刻意的做的太通用。