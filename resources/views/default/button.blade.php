<?php /** @var \Lego\Foundation\Button $button */ ?>

@if($url = $button->getUrl())
    <a href="{{ $url }}" {!! $attributes !!}>{{ $button->getText() }}</a>
@else
    <button {!! $attributes !!}>{{ $button->getText() }}</button>
@endif

@if($button->isPreventRepeatClick())
    @push('lego-scripts')
        <script>
            $('#{{ $button->getId() }}').on('click', function () {
                var button = this;
                setTimeout(function () {
                    $(button).attr('disabled', true).attr('href', 'javascript:;');
                }, 0);
            });
        </script>
    @endpush
@endif
