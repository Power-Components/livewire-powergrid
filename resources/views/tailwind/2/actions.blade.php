@if(isset($actionBtns) && count($actionBtns))
    @foreach($actionBtns as $action)
        <td class="px-1 py-1 whitespace-nowrap" style="width: 50px;">

            @php
                $parameters = [];
                foreach ($action->param as $param => $value) {
                    $parameters[$param] = $row->{$value};
                }
            @endphp

            <form target="_blank" action="{{ route($action->route, $parameters) }}" method="post">
                @method($action->method)
                @csrf

                <button type="submit" class="
                 {{ (filled($action->class)) ? 'focus:outline-none text-sm py-2.5 px-5 rounded border '.$action->class
                                :'focus:outline-none text-sm py-2.5 px-5 rounded border'
                 }}"
                >
                    {{ (filled($action->caption)) ? $action->caption: 'Editar' }}
                </button>
            </form>


        </td>
    @endforeach
@endif
