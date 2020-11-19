@if(config('lego.assets.global.jQuery') || isset($legoInternalView))
    <script src="/packages/wutongwan/lego/externals/jquery/jquery.min.js"></script>
@endif
@if(config('lego.assets.global.bootstrap') || isset($legoInternalView))
    <script src="/packages/wutongwan/lego/externals/bootstrap/js/bootstrap.min.js"></script>
@endif
{{-- webpack scripts --}}
