<?php namespace Lego;

use Collective\Html\HtmlFacade;

/**
 * Lego 中静态文件的依赖维护类
 *
 * 用例，例如在 AutoComplete Field 中需要添加自己的脚本文件
 *
 *      \Lego\LegoAsset::script('path-to-script.js')
 *
 * 使用 Lego 的页面需要在
 *
 *  - 页面 header 中添加
 *      \Lego\LegoAsset::styles();
 *
 *  - 页面底部，body 标签内添加
 *
 *      \Lego\LegoAsset::scripts();
 *
 * Class LegoAsset
 * @package Lego
 */
class LegoAsset
{
    const ASSET_PATH = 'packages/wutongwan/lego';

    private static $styles = [];
    private static $scripts = [];

    public static function css($path, $isLegoAsset = true)
    {
        $path = self::path($path, $isLegoAsset);
        if (!in_array($path, self::$styles)) {
            self::$styles [] = $path;
        }
    }

    public static function js($path, $isLegoAsset = true)
    {
        $path = self::path($path, $isLegoAsset);
        if (!in_array($path, self::$scripts)) {
            self::$scripts [] = $path;
        }
    }

    private static function path($path, $isLegoAsset = true)
    {
        return $isLegoAsset ? self::ASSET_PATH . '/' . trim($path, '/') : $path;
    }

    public static function scripts()
    {
        return join("\n", array_map(
            function ($script) {
                return HtmlFacade::script($script);
            },
            self::$scripts
        ));
    }

    public static function styles()
    {
        return join("\n", array_map(
            function ($style) {
                return HtmlFacade::style($style);
            },
            self::$styles
        ));
    }
}