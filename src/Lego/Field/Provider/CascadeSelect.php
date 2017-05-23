<?php namespace Lego\Field\Provider;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Lego\Field\Field;
use Lego\Foundation\Facades\LegoAssets;
use Lego\Register\HighPriorityResponse;

class CascadeSelect extends Select
{
    const DEPEND_QUERY_KEY = '__lego_depend';

    /**
     * @var Field
     */
    protected $depend;
    private $remote;
    private $match;
    protected $validateOption = false;

    public function depend(Field $field, \Closure $match)
    {
        $this->depend = $field;
        $this->match = $match;
        $this->remote = lego_register(
            HighPriorityResponse::class,
            function () {
                $depend = Request::query(CascadeSelect::DEPEND_QUERY_KEY);
                $options = call_user_func_array($this->match, [$depend]);
                return (new Collection($options))->toArray();
            },
            md5($this->name())
        )->url();
        $this->remote = $this->remote . '&' . self::DEPEND_QUERY_KEY . '=';

        return $this;
    }

    public function getDependField()
    {
        return $this->depend;
    }

    public function getRemote()
    {
        return $this->remote;
    }

    public function getFEOptions()
    {
        if ($value = $this->depend->getNewValue()) {
            $this->options(
                (new Collection(call_user_func_array($this->match, [$value])))->toArray()
            );
        }

        return [
            'id' => $this->elementId(),
            'selected' => $this->takeDefaultInputValue(),
            'options' => $this->getOptions(),
            'depend' => $this->getDependField()->elementId(),
            'remote' => $this->getRemote(),
        ];
    }

    protected function renderEditable()
    {
        LegoAssets::js('components/vue/dist/vue.min.js');
        LegoAssets::js('components/vue-resource/dist/vue-resource.min.js');
        LegoAssets::js('field/cascade-select.js');

        return view('lego::default.field.cascade-select', ['field' => $this]);
    }
}
