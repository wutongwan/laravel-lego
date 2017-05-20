## Laravel-Lego

[![Build Status](https://travis-ci.org/wutongwan/laravel-lego.svg?branch=master)](https://travis-ci.org/wutongwan/laravel-lego)
[![Latest Stable Version](https://poser.pugx.org/wutongwan/lego/version.png)](https://packagist.org/packages/wutongwan/lego)
[![Total Downloads](https://poser.pugx.org/wutongwan/lego/d/total.png)](https://packagist.org/packages/wutongwan/lego)

* * *

*注意：lego 尚处于开发阶段，1.0版本发布之前主要接口仍有调整的可能性，请注意查看 release note ！*

* * *

## Example

- UserController.php

```php
$form = Lego::form(new User());

$form->addText('number', '编号')->required()->rule('numeric')->unique();
$form->addText('email', '邮箱')->required()->rule('email')->unique();

return $form->view('layout', ['form' => $form]);
```

- layout.blade.php

```html
<!doctype html>
<html lang="en">
<head>
    @include('lego::styles')
</head>
<body>

	{!! $form !!}

    @include('lego::scripts')
</body>
</html>
```

![image](http://ww1.sinaimg.cn/bmiddle/801b780agw1f8pjbovte0j20n80h4jrz.jpg)


## Required
  - php >= 7.0 (短时间很多人肯定还没用上 PHP7, but who cares ? 一切向前看！)
  - Laravel >= 5.2

## Installment

1、使用 Composer 添加依赖

```bash
composer require wutongwan/lego
```

2、添加 Service Provider ，将下面的内容添加到 `config/app.php` 的 `providers` 数组中

```php
// Lego 依赖 LaravelCollective/html ，所以同时需要添加其 ServiceProvider .
Collective\Html\HtmlServiceProvider::class,

Lego\LegoServiceProvider::class,
```

3、发布项目相关文件

本项目使用 [bower](https://bower.io/) 管理静态文件，执行下面命令前，请确认已安装 bower

```bash
# 1、发布配置文件
php artisan vendor:publish --provider="Lego\LegoServiceProvider"

# 2、使用 bower 更新静态文件并发布
php artisan lego:update-components
# 如果当前用户为 root
php artisan lego:update-components --bower-allow-root
```

4、仅开发环境

- IDE Helper

	为方便调用 widget 中的 addField 系列函数，推荐执行下面命令，该命令会在项目根目录创建 `_ide_helper_lego.php` 文件，方便 IDE 进行自动补全.
	
	```bash
	php artisan lego:generate-ide-helper
	```

## 文档

- [Form 表单](./docs/form.md)
- [Fields 支持的输入类型](./docs/fields.md)
- [Filter 筛选器](./docs/filter.md) 
- [Grid - 列表页](./docs/grid.md)
- [more ...](./docs/README.md)

## 开发

### 静态文件

- 依赖静态文件需在 bower.json 中配置（暂用 bower ，后期准备换到 npm + laravel-mix）

### 分支
  - master
    - 在 master 下开发及维护
  - release
    - 发布新版本前由脚本创建，添加了所有依赖的静态文件，方便 composer 安装

### 当前版本在以下环境中开发并维护
  - Mac
  - Ubuntu

* * *

## 起因

用了快一年的[zofe/rapyd-laravel](https://github.com/zofe/rapyd-laravel),整体觉得非常好用,但内部确实不少代码年久失修,比较脏，有的组件也不适合中国的情况（比如google map）。

所以我们几个小伙伴想了很久，还是决定重新造个轮子，参考rapyd的思路,做一个我们自己用着更顺手的脚手架库。

起名Lego,也是向乐高致敬,能用简单的Block搭建摩天大厦是我们的梦想。

现在整体代码还是非常初级的版本,有兴趣的朋友欢迎关注以及和我们讨论,但由于整个项目是以提高团队自身效率为最优先前提的,可能我们会独断一些,不会刻意的做的太通用。
