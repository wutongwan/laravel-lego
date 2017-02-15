@foreach(\Lego\LegoAsset::styles() as $style)
    {!! \Collective\Html\HtmlFacade::style($style) !!}
@endforeach

@stack('lego-styles')
