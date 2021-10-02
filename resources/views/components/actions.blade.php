@props([
    'actions' => null,
    'theme' => null,
    'row' => null
])
<div class="w-full md:w-auto">
    @if($row === '')
        <div class="sm:flex sm:flex-row">
            @foreach($actions as $action)
                <div class="sm:mr-2 mb-2 w-auto">

                    @if($action->event !== '')
                        <a wire:click='$emit("{{ $action->event }}", @json($action->param))'
                           target="{{ $action->target }}"
                           class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </a>
                    @elseif($action->view !== '')
                        <a wire:click='$emit("openModal", "{{$action->view}}", @json($action->param))'
                                target="{{ $action->target }}"
                                class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </a>
                    @else
                        <form target="{{ $action->target }}"
                              action="{{ route($action->route, $action->param) }}"
                              method="{{ $action->method }}">
                            @method($action->method)
                            @csrf
                            <button type="submit"
                                    class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
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
                style="{{ $theme->actions->tdBodyStyle ?? $theme->table->tdBodyStyle }}">
                @php
                    $parameters = [];
                    foreach ($action->param as $param => $value) {
                       $parameters[$param] = $row->{$value};
                    }
                @endphp

                @if($action->event !== '')
                    <a wire:click='$emit("{{ $action->event }}", @json($action->param))'
                       target="{{ $action->target }}"
                       class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                        {!! $action->caption !!}
                    </a>
                @elseif($action->view !== '')
                    <a wire:click='$emit("openModal", "{{$action->view}}", @json($action->param))'
                       target="{{ $action->target }}"
                       class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                        {!! $action->caption !!}
                    </a>
                @else
                    <form target="{{ $action->target }}"
                          action="{{ route($action->route, $parameters) }}"
                          method="{{ $action->target }}">
                        @method($action->method)
                        @csrf
                        <button type="submit"
                                class="{{ filled( $action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption ?? '' !!}
                        </button>
                    </form>
                @endif
            </td>
        @endforeach
    @endif
</div>
