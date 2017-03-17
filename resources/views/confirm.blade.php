@extends('lego::layout')

@section('__lego-simple-title')
    {{ $message }}
@stop

@push('lego-styles')
<style>
    #lego-confirm-message {
        font-size: 1.2em;
        margin-top: 3em;
        margin-bottom: 3em;
    }

    #lego-confirm-button {
        margin-right: 2em;
    }
</style>
@endpush

@section('__lego-simple-body')
    <div id="lego-confirm" class="text-center">
        <p id="lego-confirm-message">{{ $message }}</p>
        <p class="text-center">
            <a href="{{ $confirm }}" class="btn btn-primary once disabled" id="lego-confirm-button">确认</a>
            {{-- 确认按钮之所以默认 disabled 是为了防止通过禁用 JS 跳过等待时间 --}}
            <a href="{{ $cancel }}" class="btn btn-default once">取消</a>
        </p>
    </div>
@stop

@push('lego-scripts')
<script>
    $('a.once').click(function () {
        var button = this;
        setTimeout(function () {
            $(button).attr('disabled', true)
                .attr('href', 'javascript:;')
                .text('处理中...');
        }, 0);
    });

    $(document).ready(function () {
        var $confirm = $('#lego-confirm-button');
        var countdown = function (second) {
            if (second <= 0) {
                $confirm.text('确认');
                $confirm.removeClass('disabled');
            } else {
                $confirm.text((second--) + ' 秒后可确认');
                setTimeout(function () {
                    countdown(second);
                }, 1000)
            }
        };

        countdown({{ $delay }});
    });
</script>
@endpush
