@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
    'actions' => null,
    'theme' => null,
])
<div class="w-full md:w-auto">
    <div class="sm:flex sm:flex-row">
        @foreach($actions as $action)
            <div class="sm:mr-2 w-auto">
                @php
                    if($action->singleParam) {
                        $parameters = $helperClass->makeActionParameter($action->param);
                    } else {
                        $parameters = $helperClass->makeActionParameters($action->param);
                    }
                @endphp
                @if($action->event !== '' && $action->to === '')
                    <a wire:click='$emit("{{ $action->event }}", @json($parameters))'
                       target="{{ $action->target }}"
                       title="{{ $action->tooltip }}"
                       class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                        {!! $action->caption !!}
                    </a>
                @elseif($action->event !== '' && $action->to !== '')
                    <a wire:click='$emitTo("{{ $action->to }}", "{{ $action->event }}", @json($parameters))'
                       target="{{ $action->target }}"
                       title="{{ $action->tooltip }}"
                       class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                        {!! $action->caption !!}
                    </a>
                @elseif($action->view !== '')
                    <a wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))'
                       target="{{ $action->target }}"
                       title="{{ $action->tooltip }}"
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
                                    title="{{ $action->tooltip }}"
                                    class="{{ filled( $action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                                {!! $action->caption ?? '' !!}
                            </button>
                        </form>
                    @else
                        <a href="{{ route($action->route, $parameters) }}"
                           title="{{ $action->tooltip }}"
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
