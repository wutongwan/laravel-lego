<?php namespace Lego\Widget;

use Lego\Data\Data;
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
        }

        return lego_table($data->data());
    }

    /**
     * 初始化对象
     */
    protected function initialize()
    {
        $this->data()->fetch();
    }

    /**
     * @param Field $field
     */
    protected function fieldAdded(Field $field)
    {
        $field->value()->set(function () use ($field) {
            return $field->source()->get($field->column());
        });
    }

    /**
     * 渲染当前对象
     * @return string
     */
    public function render(): string
    {
        return view('lego::default.grid.table', ['grid' => $this])->render();
    }

    /**
     * Widget 的所有数据处理都放在此函数中, 渲染 view 前调用
     */
    public function process()
    {
    }

    public function rows()
    {
        return $this->data();
    }

    public function paginate(int $perPage, string $pageName = 'page', int $page = null)
    {
        $this->data()->paginate($perPage, $pageName, $page);

        return $this;
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
        foreach ($this->rows() as $row) {
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
}