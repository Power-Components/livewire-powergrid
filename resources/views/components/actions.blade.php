@props([
    'actions' => null,
    'theme' => null,
    'row' => null
])
<div class="w-1/2 md:w-auto">
    @if($row === '')
        <div class="ml-2 flex flex-row">
            @foreach($actions as $action)
                <div class="mr-2">

                    @if($action->event !== '')
                        <button wire:click='$emit("{{ $action->event }}", @json($action->param))'
                                class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </button>

                    @elseif($action->view !== '')
                        <button wire:click='$emit("openModal", "{{$action->view}}", @json($action->param))'
                                class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </button>

                    @else
                        <form @if($action->method !== 'delete') target="_blank"
                              @endif action="{{ route($action->route, $action->param) }}" method="post">
                            @method($action->method)
                            @csrf
                            <button type="submit" class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                                {!! $action->caption !!}
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if(isset($actions) && count($actions) && $row !== '')
        @foreach($actions as $action)
            <td class="{{ $theme->actions->tdBodyClass ?? $theme->table->tdBodyClass }}"
                style="{{ $theme->actions->tdBodyClass ?? $theme->table->tdBodyStyle }}">
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                       $parameters[$param] = $row->{$value};
                    }
                @endphp

                @if($action->event !== '')
                    <button wire:click='$emit("{{ $action->event }}", @json($parameters))'
                            class="{{ filled( $action->class) ? $action->class : $theme->actions->btnClass }}">
                        {!! $action->caption ?? '' !!}
                    </button>

                @elseif($action->view !== '')
                    <button wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))'
                            class="{{ filled( $action->class) ? $action->class : $theme->actions->btnClass }}">
                        {!! $action->caption ?? '' !!}
                    </button>

                @else
                    <form @if($action->method !== 'delete') target="_blank"
                          @endif action="{{ route($action->route, $parameters) }}" method="post">
                        @method($action->method)
                        @csrf
                        <button type="submit" class="{{ filled( $action->class) ? $action->class : $theme->actions->btnClass }}">
                            {!! $action->caption ?? '' !!}
                        </button>
                    </form>
                @endif
            </td>
        @endforeach
    @endif
</div>
