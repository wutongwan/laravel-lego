<?php namespace Lego\Widget;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Request;
use Lego\Data\Data;
use Lego\Data\Row\Row;
use Lego\Field\Field;
use Lego\Register\Data\ResponseData;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class Grid extends Widget
{
    protected function prepareData($data): Data
    {
        if ($data instanceof Filter) {
            $data->processFields();
            $data->process();
            $data = $data->data();
        }

        return lego_table($data);
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
    }

    /**
     * @param Field $field
     */
    protected function fieldAdded(Field $field)
    {
    }


    public function orderBy($attribute, bool $desc = false)
    {
        $this->data()->orderBy($attribute, $desc);
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

    public function export($name, \Closure $onExport = null)
    {
        /** @var ResponseData $resp */
        $resp = lego_register(
            ResponseData::class,
            function () use ($name, $onExport) {
                if ($onExport) {
                    call_user_func($onExport, $this);
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
        foreach ($this->paginator() as $row) {
            $_row = [];
            /** @var Field $field */
            foreach ($this->fields() as $field) {
                $_row[$field->description()] = $row->get($field->name());
            }
            $data [] = $_row;
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

    public function paginate(int $perPage)
    {
        $this->paginatorPerPage = $perPage;

        return $this;
    }

    /**
     * @return AbstractPaginator|Row[]
     */
    public function paginator()
    {
        if (!$this->paginator) {
            $this->paginator = $this->data()->paginate($this->paginatorPerPage);
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
            $this->addButton('right-top', $name, $url, 'lego-export-' . $name);
        }

        $this->paginator();
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return view('lego::default.grid.table', ['grid' => $this])->render();
    }
}