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
                    $when          = true;
                    $disableWhen   = false;
                    $whenFallback  = '';
                    if (filled($action->when)) {
                         $whenFallback  = '';
                         $when         = false;
                         $closureWhen  = $action->when;
                         if (is_callable($closureWhen)) {
                            $when         = $closureWhen($row);
                         }

                         if (!$when) {
                             $whenFallback = $action->whenFallback;
                         }
                     }

                    if (filled($action->disableWhen)) {
                         $disableWhen   = false;
                         $closureWhen  = $action->disableWhen;
                         if (is_callable($closureWhen)) {
                            $disableWhen         = $closureWhen($row);
                         }
                     }

                     foreach ($action->param as $param => $value) {
                         if (!empty($row->{$value})) {
                            $parameters[$param] = $row->{$value};
                         } else {
                            $parameters[$param] = $value;
                         }
                     }
                @endphp

                @if($when && $action->can)
                    @if(filled($action->event) || filled($action->view))
                        <a @if($action->event && !$disableWhen) wire:click='$emit("{{ $action->event }}", @json($parameters))'
                           @endif
                           @if($action->view && !$disableWhen) wire:click='$emit("openModal", "{{$action->view}}", @json($parameters))'
                           @endif
                           @if($disableWhen) disabled @endif
                           class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }} @if($disableWhen) opacity-50 @endif">
                            {!! $action->caption !!}
                        </a>
                    @endif
                @else
                    <div>{!! $whenFallback !!}</div>
                @endif

                @if(filled($action->route) && $action->can)
                    @if(strtolower($action->method) !== 'get')
                        @if($when)
                            <form @if(!$disableWhen) target="{{ $action->target }}"
                                  action="{{ route($action->route, $parameters) }}"
                                  method="post" @endif>
                                @method($action->method)
                                @csrf
                                <button @if($disableWhen) disabled @else type="submit" @endif
                                        class="{{ filled( $action->class) ? $action->class : $theme->actions->headerBtnClass }} @if($disableWhen) opacity-50 @endif">
                                    {!! $action->caption ?? '' !!}
                                </button>
                            </form>
                        @else
                            <div>{!! $whenFallback !!}</div>
                        @endif
                    @else
                        @if($when && $action->can)
                            <a @if($disableWhen) disabled @else href="{{ route($action->route, $parameters) }}" @endif
                               target="{{ $action->target }}"
                               class="{{ filled($action->class) ? $action->class : $theme->actions->headerBtnClass }} @if($disableWhen) opacity-50 @endif">
                                {!! $action->caption !!}
                            </a>
                        @else
                            <div>{!! $whenFallback !!}</div>
                        @endif
                    @endif
                @endif

            </td>
        @endforeach
    @endif
</div>
