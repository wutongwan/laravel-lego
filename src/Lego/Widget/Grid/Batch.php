<?php namespace Lego\Widget\Grid;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Foundation\Facades\LegoAssets;
use Lego\Lego;
use Lego\Operator\Query\Query;
use Lego\Operator\Store\Store;
use Lego\Register\HighPriorityResponse;
use Lego\Widget\Confirm;

/**
 * Grid 批处理的逻辑
 */
class Batch
{
    const IDS_QUERY_NAME = '__lego_batch_ids';

    private $name;
    private $url;

    /**
     * @var Query
     */
    private $query;

    /**
     * @var string
     */
    private $primaryKey;

    /**
     * 确认信息，没有 form 操作时可以通过 message($message) 传入
     *
     * @var string|\Closure
     */
    private $message;

    /**
     * @var \Closure
     */
    private $form;

    /**
     * @var \Closure
     */
    private $each;

    /**
     * @var \Closure
     */
    private $handle;

    public function __construct($name, Query $query, $primaryKey = 'id')
    {
        $this->name = $name;
        $this->query = $query;
        $this->primaryKey = $primaryKey;
    }

    public function name()
    {
        return $this->name;
    }

    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function primaryKey($key)
    {
        $this->primaryKey = $key;
        return $this;
    }

    public function url()
    {
        return $this->url;
    }

    public function form(\Closure $builder)
    {
        $this->form = $builder;
        return $this;
    }

    public function each(\Closure $closure)
    {
        $this->each = $closure;
        $this->register();
        return $this;
    }

    public function handle(\Closure $closure)
    {
        $this->handle = $closure;
        $this->register();
        return $this;
    }

    public function action(\Closure $action)
    {
        return $this->each($action);
    }

    private function register()
    {
        $this->url = HighPriorityResponse::register(
            __METHOD__ . $this->name(),
            function () {
                return $this->response();
            }
        );
    }

    private function response()
    {
        LegoAssets::reset();
        LegoAssets::css('components/bootstrap/dist/css/bootstrap.min.css');
        LegoAssets::js('components/jquery/dist/jquery.min.js');

        if (!$this->getIds()) {
            return $this->saveIdsResponse();
        }

        if ($this->form) {
            return $this->formResponse();
        }

        if ($this->message) {
            $message = $this->message instanceof \Closure
                ? call_user_func($this->message, $this->getDataCollection())
                : $this->message;
            return Lego::confirm($message, function ($sure) {
                return $sure ? $this->callHandleClosure() : redirect($this->exit());
            });
        }

        return $this->callHandleClosure();
    }

    private function saveIdsResponse()
    {
        if (!$ids = Request::input('ids')) {
            return view('lego::message', ['message' => '尚未选中任何记录！', 'level' => 'warning']);
        }

        $ids = array_unique(is_array($ids) ? $ids : explode(',', $ids));
        $hash = md5(Session::getId() . microtime());
        Cache::put(self::IDS_QUERY_NAME . $hash, $ids, 10);
        return Redirect::to(Request::fullUrlWithQuery([self::IDS_QUERY_NAME => $hash]));
    }

    private function formResponse()
    {
        $form = Lego::form();
        call_user_func($this->form, $form);
        $form->onSubmit(function ($form) {
            return $this->callHandleClosure($form);
        });
        return $form->view('lego::grid.action.form', ['form' => $form, 'action' => $this]);
    }

    private function callHandleClosure()
    {
        $params = func_get_args();
        $collection = $this->getDataCollection();
        if ($this->each) {
            array_unshift($params, null);
            $collection->each(function (Store $store) use ($params) {
                $params[0] = $store->getOriginalData();
                call_user_func_array($this->each, $params);
            });
            return redirect($this->exit());
        } elseif ($this->handle) {
            $collection = $collection->map(function (Store $store) {
                return $store->getOriginalData();
            });
            $response = call_user_func($this->handle, $collection, ...$params);
            return $response ?: redirect($this->exit());
        } else {
            throw new LegoException(__CLASS__ . ' does not set `handle` or `each`.');
        }
    }

    private function getDataCollection()
    {
        return $this->query->whereIn($this->primaryKey, $this->getIds())->get();
    }

    private function getIds()
    {
        if (!$key = Request::get(self::IDS_QUERY_NAME)) {
            return [];
        }
        $ids = Cache::get(self::IDS_QUERY_NAME . $key);
        return is_array($ids) ? $ids : [];
    }

    private function exit()
    {
        return Request::fullUrlWithQuery(
            array_merge(Request::query(), [
                self::IDS_QUERY_NAME => null,
                HighPriorityResponse::REQUEST_PARAM => null,
                Confirm::CONFIRM_QUERY_NAME => null,
                Confirm::FROM_QUERY_NAME => null,
            ])
        );
    }
}
