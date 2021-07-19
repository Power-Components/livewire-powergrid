@foreach($columns as $column)
    @php
        $field = $column->field;
    @endphp
    @if($column->hidden === false)
        <td class="{{ $column->bodyClass ?? '' }}"
            style="{{ $column->bodyStyle ?? '' }}"
        >
            @if($column->editable === true)
                <span class="d-flex justify-content-between">
		<div>
			@include('livewire-powergrid::bootstrap.50.components.editable')
		</div>
		<div>
			@if(count($column->clickToCopy) > 0)
                @if($column->clickToCopy['enabled'])
                    <button
                        style="width: 24px; border: 0; height: 30px; background-repeat: no-repeat;"
                        onclick="copyToClipboard(this)" value="copy"
                        class="img_copy"
                        data-value="{{ $row->$field }}"
                        title="{{ $column->clickToCopy['label'] }}"></button>
                @endif
            @endif
		</div>
	</span>
            @elseif(count($column->toggleable))
                @include('livewire-powergrid::bootstrap.50.components.toggleable')
            @else
                <span class="d-flex justify-content-between">
		<div>
			{!! $row->{$field} !!}
		</div>
		<div>
			@if(count($column->clickToCopy))
                @if($column->clickToCopy['enabled'])
                    <button
                        style="width: 24px; border: 0; height: 30px; background-repeat: no-repeat;"
                        onclick="copyToClipboard(this)" value="copy"
                        class="img_copy"
                        data-value="{{ $row->{$field} }}"
                        title="{{ $column->clickToCopy['label'] }}"></button>
                @endif
            @endif
		</div>
	</span>
            @endif
        </td>
    @endif
@endforeach
