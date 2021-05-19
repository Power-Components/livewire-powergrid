@foreach($columns as $column)
    @php
        $field = $column->field;
    @endphp
    @if($column->hidden === false)
        <td class="px-3 py-2 whitespace-nowrap {{ $column->body_class ?? '' }}"
            style=" {{ $column->body_style ?? '' }}"
        >
            @if($column->editable === true)
                <span class="flex justify-between">
<div>
@include('livewire-powergrid::tailwind.2.components.editable')
</div>
<div>
@if(count($column->click_to_copy))
        @if($column->click_to_copy['enabled'])
            <button
                style="width: 24px; height: 30px; background-repeat: no-repeat;"
                onclick="copyToClipboard(this)" value="copy"
                class="img_copy" data-value="{{ $row->$field }}"
                title="{{ $column->click_to_copy['label'] }}"></button>
        @endif
    @endif
</div>
</span>
            @elseif(count($column->toggleable) > 0)
                @include('livewire-powergrid::tailwind.2.components.toggleable')
            @else
                <span class="flex justify-between">
<div>
{!! $row->$field !!}
</div>
<div>
@if(count($column->click_to_copy))
        @if($column->click_to_copy['enabled'])
            <button
                style="width: 24px; height: 30px; background-repeat: no-repeat;"
                onclick="copyToClipboard(this)" value="copy"
                class="img_copy" data-value="{{ $row->$field }}"
                title="{{ $column->click_to_copy['label'] }}"></button>
        @endif
    @endif
</div>
</span>
            @endif
        </td>
    @endif
@endforeach
