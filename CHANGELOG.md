# Release Notes

## 0.2.14 (2018-01-20)

### Added

- Filter 支持自定义输入，默认还是 Request

## 0.2.13 (2017-12-30)

### Fixed

- Avoid installing unstable Laravel versions

## 0.2.11 (2017-12-25)

### Added

- 按钮防重复点击机制

## 0.2.7 (2017-12-17)

### Fixed

- 修正 Datetime 在移动端使用原生日期控件时的输入问题

## 0.2.6 (2017-11-01)

### Fixed

- 拿掉 Time Field 中的 date validation

## 0.2.4 (2017-11-01)

### Added

- 允许自定义表单提交按钮文本

## 0.2.3 (2017-09-14)

### Added

- Elasticsearch Query
  + Lego 使用 [Plastic](https://github.com/sleimanx2/plastic) 调用 Elasticsearch 接口，请自行安装
  + 使用方式：`$filter = Lego::filter(YourModel::search())`


## 0.2.2 (2017-09-11)

### Added

- Field 只读模式时启用 escape

## 0.2.1 (2017-09-10)

### Added

- [使用 HTML Purifier 过滤所有请求数据](docs/html-purifier.md)

  > Breaking: 
  > 
  > 本次更新中引入了新的 composer package [mewebstudio/Purifier](https://github.com/mewebstudio/Purifier)，请手动添加 ServiceProvider、发布 Assets.
  > 
  > - ServiceProvider
  >   - `Mews\Purifier\PurifierServiceProvider::class`
  > - Publish Assets
  >   - `php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"`

- Filter 页面添加了新的栅格系统布局，可以通过配置文件进行配置：

  ```php
  [
        'widgets' => [
            'filter' => [
                // Bootstrap inline form
                'default-view' => 'lego::default.filter.inline',
                // or Bootstrap grid system
                'default-view' => 'lego::default.filter.rows',
            ],
        ]
    ]
  ```



## 0.1.44 (2017-09-04)

### Added

- [Field：Scope name 默认取用 Field name](docs/filter.md#query-scope)
- [Field: 不需要存储到 Model 的 Field](docs/fields.md#不需要存储到-Model-的-Field)

## 0.1.43 (2017-08-23)

### Removed

- 取消 Save 事务

## 0.1.42 (2017-08-09)

### Added

- Text
    - emptyStringToNull: 若输入值为空字符串，存储时转换为 null

## 0.1.40 (2017-07-31)

### Fixed

- 为 RangeField 添加 scope 支持

## 0.1.39 (2017-07-26)

### Fixed
- [批处理功能在 Firefox 等浏览器中的 JS 执行时序问题](https://github.com/wutongwan/laravel-lego/commit/177869147a)

## 0.1.37 (2017-07-24)

### Added
- Grid 批处理操作可以在其他窗口打开，默认在当前 tab 打开
    - addBatch(...)->openInNewTab()
    - addBatch(...)->openInPopup($width = ..., $height = ...)
    - addBatch(...)->resetOpenTarget()

## 0.1.35 (2017-07-16)

### Added
- new Field : [RichText](docs/fields.md#richtext) [#98](https://github.com/wutongwan/laravel-lego/pull/94)
- Field Textarea : `cols($num)` and `rows($num)`

## 0.1.33 (2017-07-03)

### Added
- new Field : [Radios](docs/fields.md#radios) [#94](https://github.com/wutongwan/laravel-lego/pull/94)
- new Field : [Checkboxes](docs/fields.md#checkboxes) [#94](https://github.com/wutongwan/laravel-lego/pull/94)
- new Field : [Select2](docs/fields.md#select2) [#94](https://github.com/wutongwan/laravel-lego/pull/94)
- Field `mutate` 系列函数
- Field `removeAttribute($attribute)` 函数

### Changed
- `$field->takeDefaultShowValue()` rename to `$field->takeShowValue()` [#94](https://github.com/wutongwan/laravel-lego/pull/94)
- `$field->takeDefaultInputValue()` rename to `$field->takeInputValue()` [#94](https://github.com/wutongwan/laravel-lego/pull/94)
