<?php

namespace Lego\Widget;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Lego\Foundation\FieldName;
use Lego\Foundation\FormField;
use Lego\Input\Input;
use Lego\Input\Text;

/**
 * Class FormV2
 * @package Lego\Widget
 *
 * @method Text|FormField addText($name, $label)
 */
class FormV2
{
    /**
     * @var Container
     */
    private $container;

    /**
     * 表单原始数据来源
     * @var mixed
     */
    private $data;

    /**
     * @var FormField[]|Input[]   Input 是为了方便自动补全，FormField Proxy 了 Input 的函数调用
     */
    private $fields = [];

    public function __construct(Container $container, $data)
    {
        $this->container = $container;
        $this->data = $data;
    }

    public function process(Request $request)
    {
        foreach ($this->fields as $field) {
        }
    }

    public function __call($method, $parameters)
    {
        // eg: addText(fieldName, fieldLabel)
        if (str_starts_with($method, 'add')) {
            $inputBaseClassName = substr($method, 3);
            if ($inputBaseClassName
                && class_exists($inputClass = "Lego\\Input\\{$inputBaseClassName}")
                && is_subclass_of($inputClass, Input::class)
            ) {
                $input = $this->container->make($inputClass);
                return $this->fields[$parameters[0]] = new FormField(
                    $input,
                    new FieldName($parameters[0]),
                    $parameters[1]
                );
            }
        }

        throw new \BadMethodCallException("Method `{$method}` not found");
    }
}
