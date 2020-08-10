@if(config('lego.assets.global.jQuery'))
    <script src="/packages/wutongwan/lego/externals/jquery/jquery.min.js"></script>
@endif
@if(config('lego.assets.global.bootstrap'))
    <script src="/packages/wutongwan/lego/externals/bootstrap/js/bootstrap.min.js"></script>
@endif
{{-- webpack scripts --}}

@stack('lego-scripts')
