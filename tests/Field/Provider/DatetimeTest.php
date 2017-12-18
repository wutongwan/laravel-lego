<?php namespace Lego\Tests\Field\Provider;

use Illuminate\Support\Facades\Config;
use Lego\Field\Provider\Datetime;
use Lego\Tests\TestCase;
use Lego\Tests\Tools\FakeMobileDetect;
use Lego\Widget\Form;

class DatetimeTest extends TestCase
{
    public function testDisableNativePicker()
    {
        $js = '$("#lego-test").datetimepicker';

        $form = new Form([]);
        $field = $form->addDatetime('test');
        $this->assertContains($js, $this->render2html($form));
        $this->assertEquals('text', $field->getInputType());

        // 禁用一次
        $form = new Form([]);
        $field = $form->addDatetime('test')->disableNativePicker();
        $this->assertContains($js, $this->render2html($form));
        $this->assertEquals('text', $field->getInputType());

        // 配置文件全局禁用
        $key = 'lego.field.provider.' . Datetime::class . '.disable-native-picker';
        Config::set($key, true);
        $form = new Form([]);
        $field = $form->addDatetime('test');
        $this->assertContains($js, $this->render2html($form));
        $this->assertEquals('text', $field->getInputType());
        Config::set('lego.field.provider.' . Datetime::class . '.disable-native-picker', false);

        // 判断 UA 自动启用
        FakeMobileDetect::mockIsMobile();
        $form = new Form([]);
        $field = $form->addDatetime('test');
        $this->assertNotContains($js, $this->render2html($form));
        $this->assertEquals('datetime-local', $field->getInputType());
    }
}
