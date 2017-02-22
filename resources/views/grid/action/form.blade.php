<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $action->name() }}</title>
</head>
<body>
<?
\Lego\LegoAsset::css('components/bootstrap/dist/js/bootstrap.min.js');
?>
@include('lego::styles')

<div class="col-md-6" style="margin: 0 auto; float: none;">
    <div class="panel panel-default">
        <div class="panel-body">
            {{ $form }}
        </div>
    </div>
</div>

@include('lego::scripts')
</body>
</html>
