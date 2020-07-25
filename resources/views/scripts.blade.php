@foreach(\Lego\Foundation\Facades\LegoAssets::scripts() as $script)
    {!! \Collective\Html\HtmlFacade::script($script) !!}
@endforeach

@foreach(\Lego\UI\BootstrapJQueryUI::scripts() as $script)
    <script src="/packages/wutongwan/lego/{{ $script }}"></script>
@endforeach

<script>
    lego.register();
</script>
@stack('lego-scripts')
