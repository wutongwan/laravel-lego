<?php

namespace Lego\Widget\Grid;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Lego\Foundation\Facades\LegoAssets;
use Lego\Operator\Store;
use Lego\Register\HighPriorityResponse;
use Lego\Utility\Excel;
use Lego\Widget\Concerns as WidgetConcerns;
use Lego\Widget\Filter;
use Lego\Widget\Widget;

/**
 * Class Grid.
 *
 * @lego-ide-helper
 */
class Grid extends Widget
{
    use Concerns\HasCells,
        Concerns\HasBatch,
        WidgetConcerns\HasQueryHelpers,
        WidgetConcerns\HasPagination;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * 移动端列表页是否使用移动版.
     *
     * @var bool
     */
    protected $responsive = false;

    protected function transformer($data)
    {
        if ($data instanceof Filter) {
            $this->filter = $data;
            $this->filter->processOnce();

            return $this->filter->getQuery();
        }

        return parent::transformer($data);
    }

    protected function initialize()
    {
        $this->responsive(Config::get('lego.widgets.grid.responsive'));
    }

    public function filter()
    {
        return $this->filter;
    }

    /**
     * 移动端列表页是否使用移动版.
     *
     * @param bool $condition
     *
     * @return $this
     */
    public function responsive($condition = true)
    {
        $this->responsive = boolval($condition);

        return $this;
    }

    /**
     * 导出功能.
     *
     * @var array
     */
    private $exports = [];

    public function exports()
    {
        return $this->exports;
    }

    public function export($name, \Closure $exporting = null)
    {
        /** @var \Lego\Register\HighPriorityResponse $resp */
        $resp = lego_register(
            HighPriorityResponse::class,
            function () use ($name, $exporting) {
                if ($exporting) {
                    call_user_func($exporting, $this);
                }
                $excel = $this->exportAsExcel($name);
                Excel::download($excel);
            },
            md5('grid export' . $name)
        );
        $this->exports[$name] = $resp->url();

        return $this;
    }

    public function exportAsExcel($filename)
    {
        $data = $this->getPlainResult()->all();
        Excel::downloadFromArray($filename . '.xlsx', $data);
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用.
     */
    public function process()
    {
        foreach ($this->exports as $name => $url) {
            $this->addButton(self::BTN_RIGHT_TOP, $name, $url, 'lego-export-' . $name);
        }

        if (count($this->batches())) {
            LegoAssets::js('components/vue/dist/vue.min.js');
            LegoAssets::js('js/batch.js');
        }

        $this->paginator();
    }

    /**
     * 渲染当前对象
     *
     * @return string
     */
    public function render()
    {
        $view = $this->responsive && app(\Mobile_Detect::class)->isMobile()
            ? view('lego::default.grid.list-group')
            : view('lego::default.grid.table');

        return $view->with('grid', $this)->render();
    }

    /**
     * @return Collection
     */
    public function getKeys()
    {
        return $this->paginator()->map(function (Store $row) {
            return $row->getKey();
        });
    }

    public function getResult($plain = false)
    {
        $result = new Collection();

        foreach ($this->paginator() as $row) {
            $line = [];
            foreach ($this->cells() as $cell) {
                /** @var Cell $cell */
                $value = $plain ? $cell->fill($row)->getPlainValue() : (string) $cell->fill($row)->value();
                $line[$cell->description()] = $value;
            }
            $result->push($line);
        }

        return $result;
    }

    public function getPlainResult()
    {
        return $this->getResult(true);
    }
}
