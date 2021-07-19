@props([
    'actions' => null,
    'theme' => null,
    'row' => null
])
<div>
    @if(isset($actions) && count($actions))
        @foreach($actions as $action)
            <td class="{{ $theme->table->tdBodyClass. ' '. $theme->actions->tdBodyClass }}" style="{{ $theme->table->tdBodyStyle. ' '. $theme->actions->tdBodyClass }}">
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                        $parameters[$param] = $row->{$value};
                    }
                @endphp
                @if($action->event !== '')
                    <button wire:click='$emit("{{ $action->event }}", @json($parameters))'
                            class="{{ $theme->actions->btnClass. ' '. $action->class }}">
                        {!! $action->caption ?? '' !!}
                    </button>

                @elseif($action->view !== '')
                    <button wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))'
                            class="{{ $theme->actions->btnClass. ' '. $action->class }}">
                        {!! $action->caption ?? '' !!}
                    </button>

                @else
                    <form @if($action->method !== 'delete') target="_blank" @endif action="{{ route($action->route, $parameters) }}" method="post">
                        @method($action->method)
                        @csrf
                        <button type="submit" class="{{ $theme->actions->btnClass. ' '. $action->class }}">
                            {!! $action->caption ?? '' !!}
                        </button>
                    </form>
                @endif
            </td>
        @endforeach
    @endif
</div>
