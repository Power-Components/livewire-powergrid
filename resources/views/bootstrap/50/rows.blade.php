@foreach($columns as $column)
    @php
        $field = $column->field;
    @endphp
    @if($column->hidden === false)
        <td class="{{ $column->body_class ?? '' }}"
            style="{{ $column->body_style ?? '' }}"
        >
            @if($column->editable === true)
                <span class="d-flex justify-content-between">
		<div>
			@include('livewire-powergrid::bootstrap.50.components.editable')
		</div>
		<div>
			@if(count($column->click_to_copy) > 0)
                @if($column->click_to_copy['enabled'])
                    <button
                        style="width: 24px; border: 0; height: 30px; background-repeat: no-repeat;"
                        onclick="copyToClipboard(this)" value="copy"
                        class="img_copy"
                        data-value="{{ $row->$field }}"
                        title="{{ $column->click_to_copy['label'] }}"></button>
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
			@if(count($column->click_to_copy))
                @if($column->click_to_copy['enabled'])
                    <button
                        style="width: 24px; border: 0; height: 30px; background-repeat: no-repeat;"
                        onclick="copyToClipboard(this)" value="copy"
                        class="img_copy"
                        data-value="{{ $row->{$field} }}"
                        title="{{ $column->click_to_copy['label'] }}"></button>
                @endif
            @endif
		</div>
	</span>
            @endif
        </td>
    @endif
@endforeach
