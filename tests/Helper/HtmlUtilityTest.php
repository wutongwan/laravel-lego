<?php

class HtmlUtilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mergeAttributesProvider
     */
    public function testMergeAttributes($attributesArray, $result)
    {
        $real = $this->call('mergeAttributes', $attributesArray);
        $this->assertEquals($result, $real);
    }

    public function mergeAttributesProvider()
    {
        return [
            [
                [['a' => 'b'], ['a' => 'c']],
                ['a' => 'b c']
            ],
            [
                [['a' => 'b'], ['c' => 'd']],
                ['a' => 'b', 'c' => 'd'],
            ],
            [
                [['a' => 'b'], []],
                ['a' => 'b']
            ]
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