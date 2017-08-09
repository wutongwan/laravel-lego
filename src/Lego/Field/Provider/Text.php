<?php namespace Lego\Field\Provider;

use Collective\Html\FormFacade;
use Lego\Field\Concerns\FilterWhereContains;
use Lego\Field\Field;

class Text extends Field
{
    use FilterWhereContains;

    /**
     * 若输入值为空字符串，存储时转换为 null
     * @var bool
     */
    protected $emptyStringToNull = false;

    /**
     * 若输入值为空字符串，存储时转换为 null
     * @return $this
     */
    public function emptyStringToNull()
    {
        $this->emptyStringToNull = true;
        return $this;
    }

    public function syncValueToStore()
    {
        $value = $this->getNewValue();

        if ($this->emptyStringToNull && is_empty_string($value)) {
            $value = null;
        }

        $this->store->set(
            $this->getColumnPathOfRelation($this->column),
            $this->mutateSavingValue($value)
        );
    }

    public function render()
    {
        return $this->renderByMode();
    }

    protected function renderEditable()
    {
        return FormFacade::input(
            $this->getInputType(),
            $this->elementName(),
            $this->takeInputValue(),
            $this->getAttributes()
        );
    }

    /**
     * 数据处理逻辑
     */
    public function process()
    {
    }
}
