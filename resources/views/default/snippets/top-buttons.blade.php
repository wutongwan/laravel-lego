<div class="clearfix" style="margin-bottom: 5px;">
    <div class="lego-left-top-buttons pull-left">
        @foreach($widget->getButtons('left-top') as $button)
            {!! $button !!}
        @endforeach
    </div>

    <div class="lego-right-top-buttons pull-right">
        @foreach($widget->getButtons('right-top') as $button)
            {!! $button !!}
        @endforeach
    </div>
</div>
