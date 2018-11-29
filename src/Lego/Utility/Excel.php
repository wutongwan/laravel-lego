<?php // zhangwei@dankegongyu.com 

namespace Lego\Utility;

use Lego\Foundation\Exceptions\LegoExportException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Box\Spout\Writer\WriterFactory as SpoutWriter;
use Box\Spout\Common\Type as SpoutType;

class Excel
{
    /**
     * create excel from key-value array
     *
     * array example:
     *
     * [
     *      ['name' => 'zhwei', 'city' => 'beijing'],
     *      ['name' => 'tom', 'city' => 'shanghai'],
     *      ...
     * ]
     *
     * @param string $filename
     * @param array $rows
     * @throws LegoExportException
     */
    public static function downloadFromArray(string $filename, array $rows)
    {
        if ($rows && !isset($rows[0])) {
            throw new LegoExportException('$rows can not be key-value array.');
        }

        if (class_exists(\Box\Spout\Writer\WriterFactory::class)) {
            self::downloadBySpoutXlsx($filename, $rows);
            return;
        }

        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            self::downloadByPhpSpreadsheet($rows);
            return;
        }

        throw new LegoExportException(
            'lego excel export required `box/spout` or `phpoffice/phpspreadsheet`'
        );
    }

    protected static function downloadByPhpSpreadsheet(array $rows)
    {
        $spreadSheet = new Spreadsheet;
        $worksheet = $spreadSheet->getActiveSheet();

        // write header
        $headers = array_keys($rows[0]);
        for ($i = 1; $i <= count($headers); $i++) {
            $worksheet->setCellValueByColumnAndRow($i, 1, $headers[$i - 1]);
        }

        // write body
        foreach ($rows as $rowIdx => $row) {
            foreach ($headers as $columnIdx => $header) {
                $worksheet->setCellValueByColumnAndRow($columnIdx + 1, $rowIdx + 2, $row[$header] ?? null);
            }
        }

        $xlsx = new Xlsx($spreadSheet);
        $xlsx->save('php://output');
    }

    protected static function createSpoutXlsxWriter()
    {
        /** @var \Box\Spout\Writer\XLSX\Writer $writer */
        $writer = SpoutWriter::create(SpoutType::XLSX);
        return $writer;
    }

    protected static function downloadBySpoutXlsx($filename, array $rows)
    {
        $writer = static::createSpoutXlsxWriter();

        // Apple Numbers and iOS 不支持 InlineString，所以换用 SharedString
        $writer->setShouldUseInlineStrings(false);

        $writer->openToBrowser($filename);


        $header = false;
        foreach ($rows as $row) {
            if (!$header) {
                $writer->addRow(array_keys($row));
                $header = true;
            }

            $writer->addRow($row);
        }

        $writer->close();
    }

    /**
     * output to php://output
     *
     * @param $excel
     * @throws LegoExportException
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function download($excel)
    {
        if ($excel instanceof IWriter) {
            $excel->save('php://output');
            return;
        }

        throw new LegoExportException('Unsupported excel object');
    }
}
