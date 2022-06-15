@if(data_get($setUp, 'header.softDeletes'))
    <div class="btn-group">
        <button class="btn btn-secondary btn-sm dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                    <span>
                   <x-livewire-powergrid::icons.trash/>
                </span>
        </button>
        <ul class="dropdown-menu">
            <li wire:click="$emit('pg:softDeletes-{{ $tableName }}', '')">
                <a class="dropdown-item" href="#">
                    {{ __('--') }}
                </a>
            </li>
            <li wire:click="$emit('pg:softDeletes-{{ $tableName }}', 'withTrashed')">
                <a class="dropdown-item" href="#">
                    {{ __('Com excluídos') }}
                </a>
            </li>
            <li wire:click="$emit('pg:softDeletes-{{ $tableName }}', 'onlyTrashed')">
                <a class="dropdown-item" href="#">
                    {{ __('Apenas excluídos') }}
                </a>
            </li>
        </ul>
    </div>
@endif
