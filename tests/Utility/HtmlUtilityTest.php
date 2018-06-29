<?php

class HtmlUtilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mergeAttributesProvider
     */
    public function testMergeAttributes($attributesArray, $result, $expectHtml)
    {
        $real = $this->call('mergeAttributes', $attributesArray);
        $this->assertEquals($result, $real);

        $html = \Lego\Utility\HtmlUtility::renderAttributes($real);
        self::assertInstanceOf(\Illuminate\Support\HtmlString::class, $html);
        self::assertSame($expectHtml, strval($html));
    }

    public function mergeAttributesProvider()
    {
        return [
            [
                [['a' => 'b'], ['a' => 'c']],
                ['a' => 'b c'],
                'a="b c"',
            ],
            [
                [['a' => 'b'], ['c' => 'd']],
                ['a' => 'b', 'c' => 'd'],
                'a="b" c="d"',
            ],
            [
                [['a' => 'b'], []],
                ['a' => 'b'],
                'a="b"',
            ],
        ];
    }

    private function call($method, $params = [])
    {
        return forward_static_call_array(
            [\Lego\Utility\HtmlUtility::class, $method],
            $params
        );
    }
}
