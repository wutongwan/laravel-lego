@foreach(\Lego\Foundation\Facades\LegoAssets::scripts() as $script)
    {!! \Collective\Html\HtmlFacade::script($script) !!}
@endforeach

@stack('lego-scripts')
