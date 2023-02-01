<div class="px-3 py-2 bg-white space-y-2 text-sm">
    @foreach($this->detailVisibleRow as $column)
        <div><span class="font-semibold">{{ $column->title }}</span> :
            @php
                $field = $column->field.'_formatted';
                $value = $row->{$field} ?? $row->{$column->field}
            @endphp
            {{ $value }}
        </div>
    @endforeach
</div>
