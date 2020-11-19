<?php

namespace Lego\Set\Grid;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Foundation\Response\ResponseManager;
use Lego\Set\Confirm;
use Lego\Set\Form\Form;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Batch
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Closure
     */
    private $handler;

    /**
     * 处理前的提示信息
     *
     * 如果设定 Closure 在执行时会接受两个参数
     *  - 选中的记录数组
     *  - 表单对象（如果存在表单）
     *
     * @var string|Closure(array, Form|null):string
     */
    private $confirm;

    /**
     * @var Closure(Form, array):void
     */
    private $form;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var int
     */
    private $limit = 100;

    /**
     * 触发当前批处理的 URL
     *
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $respId;

    /**
     * 批处理行为执行前的初始化逻辑
     * @var Closure(int[]|string[]):array
     */
    private $rowsRetriever;

    /**
     * @var Factory
     */
    private $view;

    public function __construct(string $name, Closure $rowsRetriever, Container $container, ResponseManager $responseManager, Factory $view)
    {
        $this->name = $name;
        $this->container = $container;
        $this->rowsRetriever = $rowsRetriever;
        $this->view = $view;

        // 注册批处理响应
        $this->respId = "Grid-Batch-{$name}";
        $responseManager->registerHandler($this->respId, [$this, 'response']);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRespId()
    {
        return $this->respId;
    }

    /**
     * 限定批处理记录上限
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function confirm($confirm): self
    {
        lego_assert(
            is_string($confirm) || $confirm instanceof Closure,
            '$confirm must be string or Closure'
        );

        $this->confirm = $confirm;
        return $this;
    }

    /**
     * @param Closure(Form, array):void $closure
     * @return $this
     */
    public function form(Closure $closure): self
    {
        $this->form = $closure;
        return $this;
    }

    /**
     * 每次处理单条记录
     * @param Closure(mixed):void $each    每条记录的处理行为
     * @param Closure():Response $response 所有记录处理完成后，调用此回调生成 Response 对象
     * @return $this
     */
    public function each(Closure $each, Closure $response): self
    {
        return $this->handle(function ($rows) use ($each, $response) {
            foreach ($rows as $row) {
                $each($row);
            }
            return $this->container->call($response);
        });
    }

    /**
     * 一次性处理所有选中记录
     *
     * handler 支持返回 Response 对象
     *
     * @param Closure(array, Form|null):Response $handler
     * @return $this
     */
    public function handle(Closure $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function response(Request $request, ResponseManager $responseManager)
    {
        $idsCount = (int)$request->query('__lego_ids_count');
        $ids = array_unique(explode(',', $request->query('__lego_ids')));
        if ($idsCount !== count($ids) || $idsCount > $this->limit) { // 传入大量 id 时可能会导致 url 超长，此处使用个数进行验证
            throw new InvalidArgumentException('选中记录过多，无法进行批处理操作');
        }

        $rows = $this->container->call($this->rowsRetriever, ['ids' => $ids]);

        if ($this->form) {
            return $this->formResponse($rows, $responseManager);
        }

        if ($this->confirm) {
            return $this->confirmResponse($rows);
        }

        return $this->runHandler($rows);
    }

    private function formResponse(array $rows, ResponseManager $responseManager)
    {
        $form = $this->container->make(Form::class, ['model' => []]);
        call_user_func_array($this->form, [$form, $rows]);
        $form->onSuccess(function (array $formData) use ($rows) {
            return $this->runHandler($rows, $formData);
        });
        return $responseManager
            ->registerSet($form)
            ->view('lego::bootstrap3.internal', ['set' => $form]);
    }

    private function confirmResponse(array $rows)
    {
        $message = is_string($this->confirm) ? $this->confirm : call_user_func_array($this->confirm, [$rows]);
        $confirm = new Confirm($message, function () use ($rows) {
            return $this->runHandler($rows);
        });
        $this->container->call([$confirm, 'process']);
        return $confirm->response();
    }

    private function runHandler(array $rows, array $formData = []): SymfonyResponse
    {
        $response = call_user_func_array($this->handler, [$rows, $formData]);
        if (is_string($response)) {
            return new Response($this->view->make('lego::bootstrap3.message', ['message' => $response]));
        }
        if ($response instanceof SymfonyResponse) {
            return $response;
        }
        throw new LegoException('handle() callback must return string message or Symfony Response Object');
    }
}
