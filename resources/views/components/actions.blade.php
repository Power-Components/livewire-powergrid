@props([
    'actions' => null,
    'theme' => null,
    'row' => null
])
<div class="w-full md:w-auto">
    @if(isset($actions) && count($actions) && $row !== '')
        @foreach($actions as $action)
            <td class="pg-actions {{ $theme->table->tdBodyClass }}"
                style="{{ $theme->table->tdBodyStyle }}">
                @php
                    $can  = $action->can;
                    $when = true;
                    if (filled($action->when)) {
                         $when = false;
                         $actionWhen = $action->when;
                         $when = $actionWhen($row);
                         unset($actionWhen);
                     }
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
                    @if($when)
                        <a wire:click='$emit("{{ $action->event }}", @json($parameters))'
                           class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </a>
                    @endif
                @elseif($action->view !== '')
                    @if($when && $can)
                        <a wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))'
                           class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                            {!! $action->caption !!}
                        </a>
                    @endif
                @else
                    @if(strtolower($action->method) !== ('get'))
                        @if($when && $can)
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
                        @endif
                    @else
                        @if($when && $can)
                            <a href="{{ route($action->route, $parameters) }}"
                               target="{{ $action->target }}"
                               class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }}">
                                {!! $action->caption !!}
                            </a>
                        @endif
                    @endif
                @endif
            </td>
        @endforeach
    @endif
</div>
