<?php namespace Lego\Widget\Grid;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Request;
use Lego\Foundation\Facades\LegoAssets;
use Lego\Register\HighPriorityResponse;
use Lego\Widget\Filter;
use Lego\Widget\Widget;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

/**
 * Class Grid
 * @method \Lego\Foundation\Button addRightTopButton($text, $url = null, $id = null)
 * @method \Lego\Foundation\Button addRightBottomButton($text, $url = null, $id = null)
 * @method \Lego\Foundation\Button addLeftTopButton($text, $url = null, $id = null)
 * @method \Lego\Foundation\Button addLeftBottomButton($text, $url = null, $id = null)
 * @package Lego\Widget\Grid
 */
class Grid extends Widget
{
    use Concerns\HasCells, Concerns\HasBatch;

    /**
     * @var Filter
     */
    protected $filter;

    protected function transformer($data)
    {
        if ($data instanceof Filter) {
            $this->filter = $data;
            $this->filter->process();
            return $this->filter->getQuery();
        }

        return parent::transformer($data);
    }

    public function filter()
    {
        return $this->filter;
    }

    public function orderBy($attribute, bool $desc = false)
    {
        $this->query->orderBy($attribute, $desc);

        return $this;
    }

    /**
     * 导出功能
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
                return $this->exportAsExcel($name);
            },
            md5('grid export' . $name)
        );
        $this->exports[$name] = $resp->url();

        return $this;
    }

    private function exportAsExcel($filename)
    {
        $data = [];
        foreach ($this->paginator() as $store) {
            $row = [];
            foreach ($this->cells() as $cell) {
                $row[$cell->description()] = $cell->fill($store)->getPlainValue();
            }
            $data[] = $row;
        }

        return Excel::create(
            $filename,
            function (LaravelExcelWriter $excel) use ($data) {
                $excel->sheet('SheetName',
                    function (\PHPExcel_Worksheet $sheet) use ($data) {
                        $sheet->fromArray($data);
                    }
                );
            }
        )->export('xls');
    }

    /**
     * @var AbstractPaginator
     */
    private $paginator;

    /**
     * how many rows per page
     * @var int
     */
    private $paginatorPerPage = 100;
    private $paginatorPageName;

    public function paginate(int $perPage, $pageName = null)
    {
        $this->paginatorPerPage = $perPage;
        $this->paginatorPageName = $pageName;

        return $this;
    }

    public function paginator()
    {
        if (!$this->paginator) {
            $this->paginator = $this->query->paginate(
                $this->paginatorPerPage,
                null,
                $this->paginatorPageName
            );
            $this->paginator->appends(Request::input());
        }

        return $this->paginator;
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    public function process()
    {
        foreach ($this->exports as $name => $url) {
            $this->addButton(self::BTN_RIGHT_TOP, $name, $url, 'lego-export-' . $name);
        }

        if ($this->batches()) {
            LegoAssets::js('components/icheck/icheck.min.js');
            LegoAssets::css('components/icheck/skins/square/blue.css');
        }

        $this->paginator();
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render()
    {
        return view('lego::default.grid.table', ['grid' => $this])->render();
    }
}
