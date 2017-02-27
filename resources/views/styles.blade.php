@foreach(\Lego\Foundation\Facades\LegoAssets::styles() as $style)
    {!! \Collective\Html\HtmlFacade::style($style) !!}
@endforeach

@stack('lego-styles')
