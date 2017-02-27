<?php namespace Lego\Widget\Grid;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Lego\Foundation\Facades\LegoAssets;
use Lego\Lego;
use Lego\Operator\Query\Query;
use Lego\Operator\Store\Store;
use Lego\Register\HighPriorityResponse;

/**
 * Grid 批处理的逻辑
 */
class Batch
{
    const IDS_KEY = '__lego_batch_ids';

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
                LegoAssets::reset();
                LegoAssets::css('components/bootstrap/dist/css/bootstrap.min.css');

                if (!$this->getIds()) {
                    return $this->fillIdsResponse();
                }

                LegoAssets::js('components/jquery/dist/jquery.min.js');
                return $this->formBuilder ? $this->formResponse() : $this->actionResponse();
            }
        );

        return $this;
    }

    private function fillIdsResponse()
    {
        if (!$ids = Request::input('ids')) {
            return view('lego::grid.action.message', ['message' => '尚未选中任何记录！', 'level' => 'warning']);
        }

        $ids = array_unique(is_array($ids) ? $ids : explode(',', $ids));
        $hash = md5(Session::getId() . microtime());
        Cache::put(self::IDS_KEY . $hash, $ids, 10);
        return Redirect::to(Request::fullUrlWithQuery([self::IDS_KEY => $hash]));
    }

    private function getIds()
    {
        if (!$key = Request::get(self::IDS_KEY)) {
            return [];
        }
        $ids = Cache::get(self::IDS_KEY . $key);
        return is_array($ids) ? $ids : [];
    }

    private function actionResponse()
    {
        $this->eachStore(function (Store $store) {
            call_user_func($this->action, $store->getOriginalData());
        });
        return redirect($this->exit());
    }

    private function formResponse()
    {
        $form = Lego::form();
        call_user_func($this->formBuilder, $form);
        $form->onSubmit(function ($form) {
            $this->eachStore(function (Store $store) use ($form) {
                call_user_func_array($this->action, [$store->getOriginalData(), $form]);
            });
            return redirect($this->exit());
        });
        return $form->view('lego::grid.action.form', ['form' => $form, 'action' => $this]);
    }

    private function eachStore(\Closure $closure)
    {
        $this->query->whereIn($this->primaryKey, $this->getIds())->get()->each($closure);
    }

    private function exit()
    {
        $query = Request::query();
        $query[HighPriorityResponse::REQUEST_PARAM] = null;
        $query[self::IDS_KEY] = null;

        return Request::fullUrlWithQuery($query);
    }
}
