<?php namespace Lego\Widget\Grid;

use Illuminate\Support\Facades\Request;
use Lego\Field\Provider\Hidden;
use Lego\Lego;
use Lego\Operator\Query\Query;
use Lego\Operator\Store\Store;
use Lego\Register\HighPriorityResponse;

/**
 * Grid 批处理的逻辑
 */
class Batch
{
    private $name;
    private $url;
    private $formBuilder;
    private $action;
    /**
     * @var Query
     */
    private $query;

    public function __construct($name, Query $query, \Closure $action = null)
    {
        $this->name = $name;
        $this->query = $query;

        if ($action) {
            $this->action($action);
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function url()
    {
        return $this->url;
    }

    public function form(\Closure $builder)
    {
        $this->formBuilder = $builder;

        return $this;
    }

    public function action(\Closure $action)
    {
        $this->action = $action;

        if ($this->formBuilder) {
            $this->url = $this->register('form', function () {
                $form = Lego::form();
                call_user_func($this->formBuilder, $form);
                $form->addField(
                    (new Hidden('ids'))->setOriginalValue(join(',', $this->getIdsFromRequest()))
                );
                $form->onSubmit(function () {
                    return redirect(HighPriorityResponse::exitUrl());
                });
                return $form->view('grid.action.form', compact('form'))->with('action', $this);
            });
        } else {
            $this->url = $this->register('action', function () {
                if ($ids = $this->getIdsFromRequest()) {
                    $this->query->whereIn('id', $ids)->get()
                        ->each(function (Store $store) {
                            call_user_func($this->action, $store->getOriginalData());
                        });
                    return redirect(HighPriorityResponse::exitUrl());
                }
            });
        }


        return $this;
    }

    private function getIdsFromRequest()
    {
        return array_unique(Request::input('ids', []));
    }

    private function register($type, \Closure $closure)
    {
        return lego_register(HighPriorityResponse::class, $closure, md5(__METHOD__ . $this->name() . $type))->url();
    }
}
