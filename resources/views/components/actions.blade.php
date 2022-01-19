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
                    $actionParameters = $helperClass->makeActionParameters($action->param, $row);
                    $rules            = $helperClass->makeActionRules($action, $row);

                    $ruleRedirect     = data_get($rules, 'redirect');
                    $ruleDisabled     = data_get($rules, 'disable');
                    $ruleHide         = data_get($rules, 'hide', false);
                    $ruleSetAttribute = data_get($rules, 'setAttribute');
                    $ruleEmit         = data_get($rules, 'emit');
                    $ruleCaption      = data_get($rules, 'caption');

                    if (filled($ruleEmit)) {
                        $event['event']  = $ruleEmit['event'];
                        $event['params'] = $helperClass->makeActionParameters(data_get($ruleEmit, 'params', []), $row);
                    } else {
                        $event['event']  = $action->event;
                        $event['params'] = $actionParameters;
                    }
                @endphp
                <div class="w-full md:w-auto"
                     style="display: {{ $ruleHide ? 'none': 'block' }}"
                >
                    @if((filled($action->event) || filled($action->view)) && is_null($ruleRedirect))
                        <button @if($event) wire:click='$emit("{{ $event['event'] }}", @json($event['params']))'
                            @endif
                            @if($action->view) wire:click='$emit("openModal", "{{$action->view}}", @json($actionParameters))'
                            @endif
                            @if($ruleDisabled) disabled @endif

                            @if(isset($ruleSetAttribute['attribute']))
                                {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($class) }}
                            @else
                                 class="$class"
                            @endif
                        >
                            {!! $ruleCaption ?? $action->caption !!}
                        </button>
                    @endif

                    @if(filled($ruleRedirect))
                        <a href="{{ $ruleRedirect['url'] }}"
                           target="{{ $ruleRedirect['target'] }}"
                           @if($ruleDisabled) disabled @endif

                           @if(isset($ruleSetAttribute['attribute']))
                           {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($class) }}
                           @else
                           class="{{ $class }}"
                           @endif
                        >
                            {!! $ruleCaption ?? $action->caption !!}
                        </a>
                    @endif

                    @if(filled($action->route))
                        @if(strtolower($action->method) !== 'get')
                            <form target="{{ $action->target }}"
                                  action="{{ route($action->route, $actionParameters) }}"
                                  method="post">
                                @method($action->method)
                                @csrf
                                <button type="submit"
                                @if($ruleDisabled) disabled @endif
                                @if(isset($ruleSetAttribute['attribute']))
                                    {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($class) }}
                                    @else
                                    class="{{ $class }}"
                                    @endif>
                                    {!! $ruleCaption ?? $action->caption !!}
                                </button>
                            </form>
                        @else
                            <a href="{{ route($action->route, $actionParameters) }}"
                               target="{{ $action->target }}"
                               @if(isset($ruleSetAttribute['attribute']))
                               {{ $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($class) }}
                               @else
                               class="{{ $class }}"
                               @endif
                            >
                                {!! $ruleCaption ?? $action->caption !!}
                            </a>
                        @endif
                    @endif
                </div>
            </td>
        @endforeach
    @endif
</div>
