@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
    'actions' => null,
    'theme' => null,
    'row' => null,
    'tableName' => null,
])
<div>
    @if(isset($actions) && count($actions) && $row !== '')
            <td class="pg-actions {{ $theme->table->tdBodyClass }}"
                style="{{ $theme->table->tdBodyStyle }}">
                @foreach($actions as $key => $action)
                    @php
                        $actionClass = new \PowerComponents\LivewirePowerGrid\Helpers\Actions(
                            $action,
                            $row,
                            $primaryKey,
                            $theme,
                        );
                    @endphp

                    @if(!boolval($actionClass->ruleHide))
                        @if($actionClass->isButton)
                            <button {{ $actionClass->getAttributes() }}>
                                {!! $actionClass->caption() !!}
                            </button>
                        @endif

                        @if(filled($actionClass->bladeComponent))
                            <x-dynamic-component :component="$actionClass->bladeComponent"
                                                 :attributes="$actionClass->bladeComponentParams"/>
                        @endif

                        @if($actionClass->isLinkeable)
                            <a {{ $actionClass->getAttributes() }}>
                                {!! $actionClass->caption() !!}
                            </a>
                        @endif

                        @if(filled($action->route) && !$actionClass->isButton)
                            @if(strtolower($action->method) !== 'get')
                                <form target="{{ $action->target }}"
                                      action="{{ route($action->route, $actionClass->parameters) }}"
                                      method="post">
                                    @method($action->method)
                                    @csrf
                                    <button type="submit"
                                        {{ $actionClass->getAttributes() }}>
                                        {!! $ruleCaption ?? $action->caption !!}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route($action->route, $actionClass->parameters) }}"
                                   target="{{ $action->target }}"
                                    {{ $actionClass->getAttributes() }}
                                >
                                    {!! $actionClass->caption() !!}
                                </a>
                            @endif
                        @endif
                    @endif
                @endforeach
            </td>

    @endif
</div>
