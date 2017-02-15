@foreach(\Lego\LegoAsset::scripts() as $script)
    {!! \Collective\Html\HtmlFacade::script($script) !!}
@endforeach

@stack('lego-scripts')
