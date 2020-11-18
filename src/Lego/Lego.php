<?php

namespace Lego;

use Lego\Foundation\Response\ResponseManager;
use Lego\Set\Form\Form as FormSet;
use Lego\Widget\Confirm;
use Lego\Widget\Filter;
use Lego\Widget\Form;
use Lego\Widget\Grid\Grid;

class Lego
{
    /**
     * Lego version.
     */
    const VERSION = '1.0';

    public static function filter($source)
    {
        return new Filter($source);
    }

    public static function grid($source = [])
    {
        return new Grid($source);
    }

    public static function form($source = [])
    {
        return new Form($source);
    }

    public static function formV2($model): FormSet
    {
        return self::make(FormSet::class, ['model' => $model]);
    }

    public static function filterV2($query): \Lego\Set\Filter\Filter
    {
        return self::make(\Lego\Set\Filter\Filter::class, ['query' => $query]);
    }

    public static function gridV2($query): \Lego\Set\Grid\Grid
    {
        if ($query instanceof \Lego\Set\Filter\Filter) {
            return self::make(\Lego\Set\Grid\FilterGrid::class, ['filter' => $query]);
        }
        return self::make(\Lego\Set\Grid\Grid::class, ['query' => $query]);
    }


    private static function make($setClass, array $parameters)
    {
        $set = app($setClass, $parameters);
        app(ResponseManager::class)->registerWidget($set);
        return $set;
    }

    public static function confirm($message, callable $action, $delay = null, string $view = null)
    {
        return (new Confirm($message, $action, $delay, $view))->response();
    }

    public static function message($message, $level = 'default')
    {
        return view('lego::default.message', compact('message', 'level'));
    }

    public static function view($view = null, $data = [], $mergeData = [])
    {
        return app(ResponseManager::class)->view($view, $data, $mergeData);
    }
}
