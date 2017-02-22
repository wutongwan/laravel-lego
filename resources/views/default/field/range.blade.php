<div class="input-group" id="{{ $field->elementId() }}">
    {{ $field->getLower()->toHtmlString() }}
    <span class="input-group-addon"> 至 </span>
    {{ $field->getUpper()->toHtmlString() }}
</div>
