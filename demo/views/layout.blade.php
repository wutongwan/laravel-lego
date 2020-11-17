<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="renderer" content="webkit">
    <title>{{ $title }} &middot; Laravel Lego</title>
    @include('lego::styles')
    <link href="//cdn.bootcss.com/highlight.js/9.9.0/styles/github.min.css" rel="stylesheet">
    <style>
        #body {
            margin: 0 auto;
            float: none;
            padding-top: 70px;
        }

        pre code {
            line-height: 2em;
            /** disable like break **/
            white-space: pre;
            word-break: normal;
            word-wrap: normal;
        }
    </style>
</head>
<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                Laravel Lego
                <span style="font-size: 0.8em; color: #888888;">&middot; Save you from CRUD</span>
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/init-database?back={{ urlencode(request()->fullUrl()) }}" style="color: red;">Reset Database</a></li>
                <li><a href="javascript:;" data-toggle="modal" data-target="#debugModal">Debug</a></li>
                <li><a href="https://github.com/wutongwan/laravel-lego" target="_blank">GitHub</a></li>
                <li><a href="https://github.com/zhwei" target="_blank">Author：@zhwei</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div id="body" class="col-md-12">
    <div class="row">
        @foreach(array_chunk($demos, 5, true) as $items)
            <div class="col-md-3 col-lg-2 col-sm-4">
                <ul>
                    @foreach($items as $item => $name)
                        <li><a href="{{ route('demo', $item) }}">{{ $name }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <hr>

    @yield('content')
</div>

<div class="clearfix"></div>
<hr>
<footer class="text-center">
    <p>
        GitHub：
        <a href="https://github.com/wutongwan/laravel-lego" target="_blank">Lego</a>
        &mid;
        Author：<a href="https://github.com/zhwei" target="_blank">@zhwei</a>
    </p>
</footer>

<div class="modal fade" tabindex="-1" role="dialog" id="debugModal" aria-labelledby="debugModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">SQL Log</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th style="width: 12em;">Time</th>
                        <th style="width: 10em;">Connection</th>
                        <th style="width: 5em;">Cost</th>
                        <th>SQL</th>
                    </tr>
                    @foreach($sqlList as $event)
                        <tr>
                            <td>{{ $event->queryAt }}</td>
                            <td>{{ $event->connectionName }}</td>
                            <td>{{ $event->time }}</td>
                            <td>
                                <pre style="white-space: inherit;"><code class="sql">{{ $event->sql }}</code></pre>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('lego::scripts')

<script src="//cdn.bootcss.com/highlight.js/9.9.0/highlight.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('pre code').forEach(function (el) {
            hljs.highlightBlock(el);
        })
    });
</script>

<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
    ga('create', 'UA-92608474-1', 'auto');
    ga('send', 'pageview');
</script>

</body>
</html>
