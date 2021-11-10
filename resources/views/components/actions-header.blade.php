@props([
    'actions' => null,
    'theme' => null,
])
<div class="w-full md:w-auto">
    <div class="sm:flex sm:flex-row">
        @foreach($actions as $action)
            <div class="sm:mr-2 mb-2 w-auto">
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                        if (!empty($row->{$value})) {
                            $parameters[$param] = $row->{$value};
                        } else {
                            $parameters[$param] = $value;
                        }
                    }
                @endphp

                @if($action->event !== '')
                    <a wire:click='$emit("{{ $action->event }}", @json($parameters))'
                       target="{{ $action->target }}"
                       class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                        {!! $action->caption !!}
                    </a>
                @elseif($action->view !== '')
                    <a wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))'
                       target="{{ $action->target }}"
                       class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                        {!! $action->caption !!}
                    </a>
                @else
                    @if(strtolower($action->method) !== 'get')
                        <form target="{{ $action->target }}"
                              action="{{ route($action->route, $parameters) }}"
                              method="{{ $action->method }}">
                            @method($action->method)
                            @csrf
                            <button type="submit"
                                    class="{{ filled( $action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                                {!! $action->caption ?? '' !!}
                            </button>
                        </form>
                    @else
                        <a href="{{ route($action->route, $parameters) }}"
                           target="{{ $action->target }}"
                           class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </a>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</div>
