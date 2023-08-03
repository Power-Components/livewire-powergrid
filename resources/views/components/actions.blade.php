@inject('helperClass', 'PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@inject('actionRenderClass', 'PowerComponents\LivewirePowerGrid\Helpers\ActionRender')

@props([
    'actions' => null,
    'theme' => null,
    'row' => null,
    'tableName' => null,
])
<div>
{{--    @if (isset($actions) && count($actions) && $row !== '')--}}
{{--        <td--}}
{{--            class="pg-actions {{ $theme->table->tdBodyClass . ' ' . $theme->table->tdActionClass }}"--}}
{{--            style="{{ $theme->table->tdBodyStyle . ' ' . $theme->table->tdActionStyle }}"--}}
{{--        >--}}
{{--            @foreach ($actions as $key => $action)--}}
{{--                @php--}}
{{--                    $customAction = null;--}}
{{--                    $actionClass = new \PowerComponents\LivewirePowerGrid\Helpers\Actions($action, $row, $primaryKey, $theme);--}}
{{--                @endphp--}}


{{--                @if (!boolval($actionClass->ruleHide) && empty($customAction))--}}
{{--                    @if ($actionClass->customRender)--}}
{{--                        {!! $actionClass->customRender !!}--}}
{{--                    @endif--}}

{{--                    @if ($actionClass->isButton)--}}
{{--                        <button {{ $actionClass->getAttributes() }}>--}}
{{--                            {!! $actionClass->caption() !!}--}}
{{--                        </button>--}}
{{--                    @endif--}}

{{--                    @if (filled($actionClass->bladeComponent))--}}
{{--                        <x-dynamic-component--}}
{{--                            :component="$actionClass->bladeComponent"--}}
{{--                            :attributes="$actionClass->bladeComponentParams"--}}
{{--                        />--}}
{{--                    @endif--}}

{{--                    @if ($actionClass->isLinkeable)--}}
{{--                        <a {{ $actionClass->getAttributes() }}>--}}
{{--                            {!! $actionClass->caption() !!}--}}
{{--                        </a>--}}
{{--                    @endif--}}

{{--                    @if (filled(data_get($action, 'route')) && !$actionClass->isButton)--}}
{{--                        @if (strtolower(data_get($action, 'method')) !== 'get')--}}
{{--                            <form--}}
{{--                                target="{{ data_get($action, 'target') }}"--}}
{{--                                action="{{ route(data_get($action, 'route'), $actionClass->parameters) }}"--}}
{{--                                method="post"--}}
{{--                            >--}}
{{--                                @method($action->method)--}}
{{--                                @csrf--}}
{{--                                <button--}}
{{--                                    type="submit"--}}
{{--                                    {{ $actionClass->getAttributes() }}--}}
{{--                                >--}}
{{--                                    {!! $ruleCaption ?? data_get($action, 'caption') !!}--}}
{{--                                </button>--}}
{{--                            </form>--}}
{{--                        @else--}}
{{--                            <a--}}
{{--                                href="{{ route(data_get($action, 'route'), $actionClass->parameters) }}"--}}
{{--                                target="{{ data_get($action, 'target') }}"--}}
{{--                                {{ $actionClass->getAttributes() }}--}}
{{--                            >--}}
{{--                                {!! $actionClass->caption() !!}--}}
{{--                            </a>--}}
{{--                        @endif--}}
{{--                    @endif--}}
{{--                @endif--}}
{{--            @endforeach--}}
{{--        </td>--}}

{{--    @endif--}}
</div>
