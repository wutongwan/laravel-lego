<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    @include('lego::styles', ['legoInternalView' => true])
</head>
<body>

<div class="col-md-6" style="margin: 0 auto; float: none;">
    @yield('content')
</div>

@include('lego::scripts', ['legoInternalView' => true])
</body>
</html>
