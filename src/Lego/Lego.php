<?php

namespace Lego;

use Illuminate\Database\Eloquent\Model;
use Lego\Foundation\Response\ResponseManager;
use Lego\Set\Filter\Filter;
use Lego\Set\Form\Form;
use Lego\Set\Grid\FilterGrid;
use Lego\Set\Grid\Grid;

class Lego
{
    /**
     * Lego version.
     */
    const VERSION = '1.0';

    /**
     * Create Filter
     *
     * @param $query
     * @return Filter
     */
    public static function filter($query): Filter
    {
        return self::make(Filter::class, ['query' => $query]);
    }

    /**
     * Create Grid
     *
     * @param $query
     * @return Grid|FilterGrid
     */
    public static function grid($query): Grid
    {
        return $query instanceof Filter
            ? self::make(FilterGrid::class, ['filter' => $query])
            : self::make(Grid::class, ['query' => $query]);
    }

    /**
     * Create Form
     *
     * @param Model|array|object $model
     * @return Form
     */
    public static function form($model): Form
    {
        return self::make(Form::class, ['model' => $model]);
    }

    /**
     * Create Form from eloquent model class name and id.
     *
     * eg: Lego::formFindModelById(Suite::class, 1);
     *
     * @param class-string<Model> $modelName
     * @param scalar $id
     */
    public static function formFindModelById(string $modelName, $id = null)
    {
        lego_assert(is_subclass_of($modelName, Model::class), '$modelName should be subclass of Eloquent Model');

        if ($id === null) {
            $model = new $modelName;
        } else {
            $model = (new $modelName)->find($id);
            abort_unless($model, 404);
        }
        return self::form($model);
    }

    private static function make($setClass, array $parameters)
    {
        $set = app($setClass, $parameters);
        app(ResponseManager::class)->registerSet($set);
        return $set;
    }

    /**
     * Render view but handled by Response Manager
     *
     * @see view()
     */
    public static function view($view = null, $data = [], $mergeData = [])
    {
        return app(ResponseManager::class)->view($view, $data, $mergeData);
    }

    /**
     * Lego Response Endpoint
     *
     * @param callable():Response $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function response(callable $response)
    {
        return app(ResponseManager::class)->response($response);
    }

    /**
     * Reset Global States
     */
    public static function resetGlobalStates(): void
    {
        app(ResponseManager::class)->reset();
    }
}
