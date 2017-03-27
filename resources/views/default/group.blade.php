<?php /* @var \Lego\Field\Group $group  */?>

@foreach($group->fields() as $field)
    @include('lego::default.form.horizontal-form-group')
@endforeach
