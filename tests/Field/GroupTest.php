<?php namespace Lego\Tests\Field;

use Lego\Field\Group;
use Lego\Tests\TestCase;
use Lego\Widget\Form;

class GroupTest extends TestCase
{
    public function testCreateGroup()
    {
        $form = new Form([]);
        $form->group('hello');
        $this->assertEquals('hello', $form->getGroup('hello')->name());
    }

    public function testGroupAdd()
    {
        $form = new Form([]);
        $fieldInGroup = $form->group('hello')->addText('name-in-hello');
        $fieldOutOfGroup = $form->addText('name-out-of-hello');

        $group = $form->getGroup('hello');
        $this->assertTrue(in_array($fieldInGroup->name(), $group->fieldNames()));
        $this->assertNotTrue(in_array($fieldOutOfGroup->name(), $group->fieldNames()));

        $readonly = [];

        $form->group('readonly', function (Form $form, Group $group) use (&$readonly) {
            $a = $form->addText('readonly-a');
            $b = $form->addText('readonly-b');

            $readonly[] = $a;
            $readonly[] = $b;

            $this->assertTrue(in_array($a->name(), $group->fieldNames()));
            $this->assertTrue(in_array($b->name(), $group->fieldNames()));

            $form->group('inner', function (Form $form, Group $inner) use ($group) {
                $c = $form->addText('inner-field');

                $this->assertTrue(in_array($c->name(), $inner->fieldNames()));
                $this->assertTrue(in_array($c->name(), $group->fieldNames()));
            });
        })->readonly();

        foreach ($readonly as $field) {
            $this->assertTrue($field->isReadonly());
        }
    }
}
