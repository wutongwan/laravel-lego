<div class="clearfix">
    <div class="lego-left-bottom-buttons pull-left">
        @foreach($widget->getButtons('left-bottom') as $button)
            {!! $button !!}
        @endforeach
    </div>

    <div class="lego-right-bottom-buttons pull-right">
        @foreach($widget->getButtons('right-bottom') as $button)
            {!! $button !!}
        @endforeach
    </div>
</div>