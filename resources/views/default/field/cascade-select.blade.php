<?php /* @var \Lego\Field\Provider\CascadeSelect $field */ ?>

<select name="{{ $field->elementName() }}"
        id="{{ $field->elementId() }}"
        v-model="selected"
        {!! \Collective\Html\HtmlFacade::attributes($field->getFlattenAttributes()) !!}>
    <option v-for="(label, value) in options" v-bind:value="value">
        @{{ label }}
    </option>
</select>

@push('lego-scripts')
<script>
    {{--newCascadeSelect({!! json_encode($field->getFEOptions()) !!});--}}
    (function () {
        if (!window.legoCascadeSelects) {
            window.legoCascadeSelects = {registered: {}, dependency: {}};
        }

        var options = JSON.parse('{!! json_encode($field->getFEOptions()) !!}');
        var ds = new Vue({
            el: '#' + options.id,
            data: {
                selected: options.selected,
                options: options.options,
                depend: options.depend,
                remote: options.remote
            },
            watch: {
                selected: function (selected) {
                    var all = legoCascadeSelects.dependency[options.id];
                    if (all) {
                        for (i = 0; i < all.length; i++) {
                            all[i].syncOptions(selected);
                        }
                    }
                }
            },
            methods: {
                syncOptions: function (value) {
                    var that = this;
                    if (value) {
                        that.selected = '';
                        that.options = {'': '...'};
                        that.$http.get(that.remote + value).then(function (resp) {
                            that.options = resp.body;
                            that.selected = Object.keys(resp.body)[0];
                        });
                    } else {
                        that.selected = null;
                        that.options = {};
                    }
                }
            }
        });

        legoCascadeSelects.registered[options.id] = ds;
        if (legoCascadeSelects.dependency[options.depend]) {
            legoCascadeSelects.dependency[options.depend].push(ds);
        } else {
            legoCascadeSelects.dependency[options.depend] = [ds]
        }

        if (!legoCascadeSelects.registered[options.depend]) {
            $('#' + options.depend).on('change', function () {
                var all = legoCascadeSelects.dependency[options.depend];
                if (all) {
                    var value = $(this).val();
                    for (i = 0; i < all.length; i++) {
                        all[i].syncOptions(value);
                    }
                }
            });
        }
    })();

</script>
@endpush
