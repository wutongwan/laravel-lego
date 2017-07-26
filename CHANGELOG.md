# Release Notes

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
