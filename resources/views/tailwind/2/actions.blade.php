@if(isset($actionBtns) && count($actionBtns))
    @foreach($actionBtns as $action)

        <td class="px-1 py-1 whitespace-nowrap" style="width: 50px;">
            @php
                $parameters = [];
                foreach ($action->param as $param => $value) {
                    $parameters[$param] = $row->{$value};
                }
            @endphp

            @if($action->event !== '')
                <button wire:click='$emit("{{ $action->event }}", @json($parameters))' class="{{ $action->class }}">
                    {{ $action->caption ?? '' }}
                </button>

            @elseif($action->view !== '')
                <button wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))' class="{{ $action->class }}">
                    {{ $action->caption ?? '' }}
                </button>

            @else
                <form @if($action->method !== 'delete') target="_blank" @endif action="{{ route($action->route, $parameters) }}" method="post">
                    @method($action->method)
                    @csrf
                    <button type="submit" class="{{ $action->class }}">
                        {{ $action->caption ?? '' }}
                    </button>
                </form>
            @endif
        </td>
    @endforeach
@endif
