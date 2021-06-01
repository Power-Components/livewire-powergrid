@props([
    'actions' => null,
    'theme' => null,
    'row' => null
])
<div>
    @if(isset($actions) && count($actions))
        @foreach($actions as $action)

            <td class="{{ $theme->table->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle }}">
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                        $parameters[$param] = $row->{$value};
                    }
                @endphp
                @if($action->view !== '')
                    <button wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))' class="
                 {{ (filled($action->class)) ? 'focus:outline-none text-sm py-2.5 px-5 rounded border '.$action->class
                                :'focus:outline-none text-sm py-2.5 px-5 rounded border'
                 }}"
                    >
                        {{ (filled($action->caption)) ? $action->caption: 'Editar' }}
                    </button>
                @else
                    <form @if($action->method !== 'delete') target="_blank" @endif action="{{ route($action->route, $parameters) }}" method="post">
                        @method($action->method)
                        @csrf
                        <button type="submit" class=" {{ (filled($action->class)) ? 'focus:outline-none text-sm py-2.5 px-5 rounded border '.$action->class
                                :'focus:outline-none text-sm py-2.5 px-5 rounded border'
                     }}">
                            {{ (filled($action->caption)) ? $action->caption: 'Editar' }}
                        </button>
                    </form>
                @endif
            </td>
        @endforeach
    @endif

</div>
