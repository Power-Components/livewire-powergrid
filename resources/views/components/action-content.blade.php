{{--@php use Illuminate\View\ComponentAttributeBag; @endphp--}}

{{--@foreach ($actions as $key => $action)--}}
{{--    <span>--}}
{{--        @php--}}
{{--            $actionRules = collect($rules)->where('forAction', data_get($action, 'action'));--}}
{{--            $attributesBagFromRules = new ComponentAttributeBag([]);--}}
{{--            $hideFromRules = false;--}}
{{--            $bladeComponentFromRules = [];--}}

{{--            foreach ($actionRules as $rule) {--}}
{{--                $closure = data_get($rule, 'rule.when');--}}
{{--                $apply = $closure($row);--}}

{{--                if ($apply) {--}}
{{--                    if ($attributes = data_get($rule, 'rule.setAttribute')) {--}}
{{--                        foreach ($attributes as $attribute) {--}}
{{--                            if (is_string($attribute['value'])) {--}}
{{--                                $attributesBagFromRules = $attributesBagFromRules->merge([--}}
{{--                                    $attribute['attribute'] => $attribute['value'],--}}
{{--                                ]);--}}
{{--                            }--}}
{{--                        }--}}
{{--                    }--}}

{{--                    if ($hide = data_get($rule, 'rule.hide')) {--}}
{{--                        $hideFromRules = true;--}}
{{--                    }--}}

{{--                    $bladeComponentFromRules = data_get($rule, 'rule.bladeComponent');--}}
{{--                }--}}
{{--            }--}}

{{--            /** @var ComponentAttributeBag $actionAttributes */--}}
{{--            $actionAttributes = data_get($action, 'attributes');--}}

{{--            $actionAttributes = $actionAttributes->exceptProps(--}}
{{--                collect($attributesBagFromRules->getAttributes())->keys()->toArray(),--}}
{{--            )->merge($attributesBagFromRules->getAttributes());--}}

{{--            $hideWhen = data_get($action, 'hideWhen');--}}
{{--            $can = data_get($action, 'can');--}}

{{--            $show = !$hideWhen || !(bool) $hideWhen();--}}

{{--            if ($can) {--}}
{{--                $show = (bool) $can();--}}
{{--            }--}}

{{--        @endphp--}}

{{--        @if ($bladeComponentFromRules)--}}
{{--            <x-dynamic-component--}}
{{--                :component="data_get($bladeComponentFromRules, 'component')"--}}
{{--                :attributes="new ComponentAttributeBag(--}}
{{--                    array_merge($actionAttributes->getAttributes(), data_get($bladeComponentFromRules, 'params')),--}}
{{--                )"--}}
{{--            >--}}
{{--                {{ data_get($action, 'slot') }}--}}
{{--            </x-dynamic-component>--}}
{{--        @else--}}
{{--            @if (data_get($action, 'view') && $show && !$hideFromRules)--}}
{{--                <x-dynamic-component--}}
{{--                    :component="data_get($action, 'view')"--}}
{{--                    :attributes="$actionAttributes"--}}
{{--                >--}}
{{--                    {{ data_get($action, 'slot') }}--}}
{{--                </x-dynamic-component>--}}
{{--            @else--}}
{{--                @if ($tag = data_get($action, 'tag'))--}}
{{--                    @php--}}
{{--                        $slot = data_get($action, 'slot');--}}
{{--                        $element = "<{$tag} {$actionAttributes->toHtml()}>{$slot}</{$tag}>";--}}
{{--                    @endphp--}}

{{--                    @if ($show && !$hideFromRules)--}}
{{--                        {!! Blade::render($element) !!}--}}
{{--                    @endif--}}
{{--                @endif--}}
{{--            @endif--}}
{{--        @endif--}}
{{--    </span>--}}
{{--@endforeach--}}
