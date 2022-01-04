<div>
    @if($toggleColumns)
        <div class="btn-group ps-2">
            <button class="btn btn-secondary btn-sm dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span>
                   <x-livewire-powergrid::icons.eye-off width="20"/>
                </span>
            </button>
            <ul class="dropdown-menu">
                @foreach($columns as $column)
                    <li wire:click="$emit('pg:toggleColumn-{{ $tableName }}', '{{ $column->field }}')"
                        wire:key="toggle-column-{{ $column->field }}">
                        <a class="dropdown-item" href="#">
                            @if($column->hidden === false)
                                <x-livewire-powergrid::icons.eye width="20"/>
                            @else
                                <x-livewire-powergrid::icons.eye-off width="20"/>
                            @endif
                            {{ $column->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
