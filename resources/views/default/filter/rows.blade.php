<?php /** @var \Lego\Widget\Filter $filter */ ?>

@include('lego::default.filter.inline', ['filter' => $filter])

@push('lego-scripts')
    <script>
        $(document).ready(function () {
            var $filter = $('#{{ $filter->uniqueId() }}');
            $filter.removeClass('form-inline').addClass('form row');
            $filter.find('.form-group').each(function () {
                var $that = $(this);
                var times = Math.max($that.find('.form-control').length, 1);
                $that.addClass('col-sm-' + (4 * times))
                    .addClass('col-md-' + (3 * times))
                    .addClass('col-lg-' + (2 * times));
            });
        });
    </script>
@endpush
