@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
    'actions' => null,
    'theme' => null,
    'row' => null,
])
<div>
@if(isset($actions) && count($actions) && $row !== '')
    @foreach($actions as $key => $action)
        <td wire:key="action-{{ $key }}" class="pg-actions {{ $theme->table->tdBodyClass }}"
            style="{{ $theme->table->tdBodyStyle }}">
            @php
                $class            = filled($action->class) ? $action->class : $theme->actions->headerBtnClass;
                $actionParameters = $helperClass->makeActionParameters($action, $row);
                $rules            = $helperClass->makeActionRules($action, $row);

                $ruleDisabled     = data_get($rules, 'disable');
                $ruleSetAttribute = data_get($rules, 'setAttribute');
                $ruleWire         = data_get($rules, 'wire');

                if (filled($ruleWire)) {
                    $event['event']  = $ruleWire['event'];
                    $event['params'] = $helperClass->makeParameters($ruleWire['params'], $row);
                } else {
                    $event['event']  = $action->event;
                    $event['params'] = $actionParameters;
                }

                if (filled($ruleSetAttribute)) {
                    if (isset($ruleSetAttribute['attribute'])) {
                        $class .= ' '.$ruleSetAttribute['value'];
                    }
                }

            @endphp
            <div class="w-full md:w-auto" x-data="{ ruleDisabled: false }">
                @if(filled($action->event) || filled($action->view))
                    <button @if($event) wire:click='$emit("{{ $event['event'] }}", @json($event['params']))'
                       @endif
                       @if($action->view) wire:click='$emit("openModal", "{{$action->view}}", @json($actionParameters))'
                       @endif
                       :disabled="ruleDisabled"
                       class="{{ $class}}">
                       {!! $action->caption !!}
                    </button>
                @endif

                @if(filled($action->route))
                    @if(strtolower($action->method) !== 'get')
                        <form target="{{ $action->target }}"
                              action="{{ route($action->route, $actionParameters) }}"
                              method="post">
                            @method($action->method)
                            @csrf
                            <button type="submit"
                                    :disabled="ruleDisabled"
                                    class="{{ $class }}">
                                {!! $action->caption ?? '' !!}
                            </button>
                        </form>
                    @else
                        <a href="{{ route($action->route, $actionParameters) }}"
                           target="{{ $action->target }}"
                           class="{{ $class }}">
                            {!! $action->caption !!}
                        </a>
                    @endif
                @endif
            </div>
        </td>
    @endforeach
@endif
</div>
