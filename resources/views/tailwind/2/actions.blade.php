@if(isset($actionBtns) && count($actionBtns))
    @foreach($actionBtns as $action)
        <td class="px-4 py-2 whitespace-nowrap" style="width: 50px;">

            @php
                $parameters = [];
                foreach ($action->param as $param => $value) {
                    $parameters[$param] = $row->$value;
                }
            @endphp

            <form action="{{ route($action->route, $parameters) }}" method="post">
                @method($action->method)
                @csrf

                <button type="submit" class="
                 {{ (filled($action->class)) ? 'focus:outline-none text-sm py-2.5 px-5 rounded-full border '.$action->class
                                :'focus:outline-none text-sm py-2.5 px-5 rounded-full border'
                 }}"
                >
                    {{ (filled($action->caption)) ? $action->caption: 'Editar' }}
                </button>
            </form>


        </td>
    @endforeach
@endif
