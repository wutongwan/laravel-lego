{{-- Generated: 此文件基于同目录 `scripts.template.blade.php` 生成得来，请勿手动修改 --}}
@if(config('lego.assets.global.jQuery') || isset($legoInternalView))
    <script src="/packages/wutongwan/lego/externals/jquery/jquery.min.js"></script>
@endif
@if(config('lego.assets.global.bootstrap') || isset($legoInternalView))
    <script src="/packages/wutongwan/lego/externals/bootstrap/js/bootstrap.min.js"></script>
@endif
<script src="/packages/wutongwan/lego/index-b9acc858805a130fee0f.js"></script>

@stack('lego-scripts')
