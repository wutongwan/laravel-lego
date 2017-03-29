<div id="lego-hide" class="hide"></div>

@foreach($form->groups() as $group)
    <?php /* @var \Lego\Field\Group $group */?>
    @if(!$group->getCondition())
        @continue
    @endif

    @foreach($group->fields() as $target)
        <script>
            $(document).ready(function () {
                var form = '{{ $form->elementId() }}';
                var field = '{{ $group->getCondition()->field()->elementName() }}';
                var operator = '{{ $group->getCondition()->operator() }}';
                var expected = JSON.parse('{!! json_encode($group->getCondition()->expected()) !!}');
                var target = '{{ $target->elementName() }}';
                (new LegoConditionGroup('#' + form, field, operator, expected, target)).watch();
            })
        </script>
    @endforeach
@endforeach
