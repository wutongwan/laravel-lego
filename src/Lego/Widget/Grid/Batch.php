<?php namespace Lego\Widget\Grid;

use Illuminate\Support\Facades\Request;
use Lego\Field\Provider\Hidden;
use Lego\Lego;
use Lego\LegoAsset;
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
    /**
     * @var string
     */
    private $primaryKey;

    public function __construct($name, Query $query, \Closure $action = null, $primaryKey = 'id')
    {
        $this->name = $name;
        $this->query = $query;
        $this->primaryKey = $primaryKey;

        if ($action) {
            $this->action($action);
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function url()
    {
        return $this->url;
    }

    public function primaryKey($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    public function form(\Closure $builder)
    {
        $this->formBuilder = $builder;

        return $this;
    }

    public function action(\Closure $action)
    {
        $this->action = $action;

        $this->url = HighPriorityResponse::register(
            __METHOD__ . $this->name(),
            function () {
                LegoAsset::clear();
                LegoAsset::css('components/bootstrap/dist/css/bootstrap.min.css');
                LegoAsset::js('components/jquery/dist/jquery.min.js');

                return $this->formBuilder ? $this->formResponse() : $this->actionResponse();
            }
        );

        return $this;
    }

    private function actionResponse()
    {
        if (!$ids = $this->getIdsFromRequest()) {
            return view('lego::grid.action.message', ['message' => '尚未选中任何记录！', 'level' => 'warning']);
        }

        $this->eachStore(function (Store $store) {
            call_user_func($this->action, $store->getOriginalData());
        });
        return redirect(HighPriorityResponse::exit());
    }

    private function formResponse()
    {
        $form = Lego::form();
        call_user_func($this->formBuilder, $form);
        $form->addField(new Hidden('ids'))->default(Request::input('ids'));
        $form->onSubmit(function ($form) {
            // 下面这行判断挪到外面会影响 AutoComplete 等对后端发起请求的 Field 的使用
            if (!$ids = $this->getIdsFromRequest()) {
                return view('lego::grid.action.message', ['message' => '尚未选中任何记录！', 'level' => 'warning']);
            }

            $this->eachStore(function (Store $store) use ($form) {
                call_user_func_array($this->action, [$store->getOriginalData(), $form]);
            });
            return redirect(HighPriorityResponse::exit());
        });
        return $form->view('lego::grid.action.form', ['form' => $form, 'action' => $this]);
    }

    private function eachStore(\Closure $closure)
    {
        $this->query->whereIn($this->primaryKey, $this->getIdsFromRequest())->get()->each($closure);
    }

    private function getIdsFromRequest()
    {
        if (!$ids = Request::input('ids')) {
            return [];
        }
        return array_unique(is_array($ids) ? $ids : explode(',', $ids));
    }
}
