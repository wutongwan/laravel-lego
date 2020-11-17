{{-- 需要传入位置参数：$set, $left, $right  --}}
<div class="clearfix">
    <div class="pull-left">
        @foreach($set->buttons()->getByPosition($left) as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
    <div class="pull-right">
        @foreach($set->buttons()->getByPosition('RightTop') as $button)
            {{ $button->render('lego-button btn btn-default') }}
        @endforeach
    </div>
</div>
