<?php

// zhangwei@dankegongyu.com

namespace Lego\Tests\Utility;

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Lego\Tests\TestCase;
use Lego\Utility\Excel;

class ExcelTest extends TestCase
{
    protected $rows = [
        [
            'name' => 'zhwei',
            'city' => 'beijing',
        ],
        [
            'name' => 'Tom',
            'city' => 'mars',
        ],
    ];

    public function testDownloadByPhpSpreadsheet()
    {
        $class = new class() extends Excel {
            public static function testDownload($rows)
            {
                return self::downloadByPhpSpreadsheet($rows);
            }
        };

        ob_start();
        $class::testDownload($this->rows);
        $excelContent = ob_get_contents();
        ob_end_clean();

        $this->assertXlsxContentCorrect($excelContent);
    }

    public function testDownloadBySpoutXlsx()
    {
        $class = new class() extends Excel {
            protected static function createSpoutXlsxWriter()
            {
                $writer = parent::createSpoutXlsxWriter();
                $writer->setGlobalFunctionsHelper(ExcelSpoutGlobalFunctionsHelper::instance());

                return $writer;
            }

            public static function testDownload($filename, $rows)
            {
                return self::downloadBySpoutXlsx($filename, $rows);
            }
        };

        ob_start();
        $class::testDownload('test_name', $this->rows);
        $excelContent = ob_get_contents();
        ob_end_clean();

        $httpHeaders = ExcelSpoutGlobalFunctionsHelper::instance()->data['header'];
        self::assertContains('Content-Disposition: attachment; filename="test_name"', $httpHeaders);

        $this->assertXlsxContentCorrect($excelContent);
    }

    protected function assertXlsxContentCorrect($content)
    {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        file_put_contents($path, $content);

        $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
        $reader->open($path);
        $actual = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $actual[] = $row;
            }
        }
        $reader->close();

        self::assertSame(array_keys($this->rows[0]), $actual[0]);
        self::assertSame(array_values($this->rows[0]), $actual[1]);
        self::assertSame(array_values($this->rows[1]), $actual[2]);
    }
}
