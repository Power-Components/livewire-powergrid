<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\{Blade, Cache};
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\{Button, PowerGridComponent};

trait HasActions
{
    /** @var array<string, string> */
    private array $iconRenderCache = [];

    public function shouldCollectActions(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        return (new \ReflectionMethod($this, 'transformActions'))
            ->getDeclaringClass()->getName() !== PowerGridComponent::class;
    }

    /** @return array<mixed> */
    public function prepareActionRulesForRows(mixed $row, ?object $loop = null): array
    {
        if (! method_exists($this, 'actionRules')) {
            return [];
        }

        $this->resolvePlugins();

        $closure = function ($row, $loop) {
            /** @var list<array<string, mixed>> $rules */
            $rules = $this->actionRules($row);

            return collect($rules)
                ->transform(function ($rule) use ($row, $loop) {
                    $closureWhen = data_get($rule, 'rule.when');
                    $closureLoop = data_get($rule, 'rule.loop');
                    $attributes = data_get($rule, 'rule.setAttribute');

                    $apply = is_callable($closureWhen) ? $closureWhen($row) : false;
                    $applyLoop = is_callable($closureLoop) ? $closureLoop($loop) : false;

                    if (is_array($attributes) && isset($attributes['attribute']) && isset($attributes['value'])) {
                        /** @var string $attributeKey */
                        $attributeKey = $attributes['attribute'];
                        $attributes = [
                            $attributeKey => $attributes['value'],
                        ];
                    }

                    $showToggleDetail = $apply && data_get($rule, 'rule.toggleDetailVisibility')
                        ? data_get($rule, 'rule.toggleDetailVisibility') === 'show'
                        : (bool) data_get($this->setUp, 'detail.showCollapseIcon');

                    $disabled = $apply && (bool) data_get($rule, 'rule.disable');
                    $hide = $apply && (bool) data_get($rule, 'rule.hide');
                    $detailView = (array) data_get($rule, 'rule.detailView', []);

                    // Collect plugin rule modifiers dynamically
                    $pluginModifiers = [];
                    foreach ($this->plugins as $plugin) {
                        $pluginModifiers = array_merge($pluginModifiers, $plugin->processRuleModifiers((array) $rule, $apply));
                    }

                    if ($apply || $applyLoop) {
                        /** @var string $forAction */
                        $forAction = data_get($rule, 'forAction');

                        return [
                            'forAction' => $forAction,
                            'apply' => (bool) $apply,
                            'applyLoop' => (bool) $applyLoop,
                            'attributes' => $attributes,
                            'disable' => $disabled,
                            'hide' => $hide,
                            'toggleDetailView' => theme('root').($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                            ...$pluginModifiers,
                            ...$detailView,
                        ];
                    }

                    return [
                        ...$pluginModifiers,
                        'toggleDetailView' => theme('root').($showToggleDetail ? '.toggle-detail' : '.no-toggle-detail'),
                    ];
                })
                ->toArray();
        };

        /** @var string $value */
        $value = data_get($row, $this->realPrimaryKey());
        $cacheKey = "pg-prepare-action-rules-for-rows-{$this->getId()}-{$value}";

        /** @var int $cacheTtl */
        $cacheTtl = config('livewire-powergrid.cache_ttl');
        if ($cacheTtl > 0) {
            /** @var array<int, array<string, mixed>> $formattedRules */
            $formattedRules = Cache::remember($cacheKey, $cacheTtl, function () use ($closure, $row, $loop) {
                $value = $closure($row, $loop);

                return array_filter($value, function ($item) {
                    return ! empty($item);
                });
            });
        } else {
            $value = $closure($row, $loop);

            $formattedRules = array_filter($value, function ($item) {
                return ! empty($item);
            });
        }

        return $formattedRules;
    }

    /**
     * Renders the buttons returned by the component's header() method.
     */
    public function renderHeaderActions(): string
    {
        $row = (object) [];

        $actions = collect($this->header()) // @phpstan-ignore-line
            ->filter(fn ($button) => $button instanceof Button)
            ->map(fn (Button $button) => $this->resolveButtonForBlade($button, $row, []))
            ->filter(fn (array $action) => ! $action['hidden'])
            ->values()
            ->all();

        if (empty($actions)) {
            return '';
        }

        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::components.structure.actions';

        return view($viewName, ['actions' => $actions])->render();
    }

    public function renderActions(object $row): string
    {
        if (! method_exists($this, 'actions')) {
            return '';
        }

        $rules = method_exists($this, 'actionRules') ? $this->resolveActionRules($row) : [];

        $actions = collect($this->actions($row)) // @phpstan-ignore-line
            ->map(function ($button) use ($row, $rules) {
                /** @var Button $button */
                return $this->resolveButtonForBlade($button, $row, $rules);
            })
            ->filter(fn (array $action) => ! $action['hidden'])
            ->values()
            ->all();

        if (empty($actions)) {
            return '';
        }

        /** @var view-string $viewName */
        $viewName = 'livewire-powergrid::components.structure.actions';

        return view($viewName, ['actions' => $actions])->render();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rules
     * @return array<string, mixed>
     *
     * @throws \Throwable
     */
    private function resolveButtonForBlade(Button $button, object $row, array $rules): array
    {
        $can = $button->can instanceof \Closure
            ? ($button->can)($row)
            : $button->can;

        if (! $can) {
            return ['hidden' => true, 'replaceHtml' => ''];
        }

        if (! empty($button->view)) {
            return [
                'hidden' => false,
                'replaceHtml' => view($button->view, ['action' => $button, 'row' => $row])->render(),
            ];
        }

        $attributes = $button->attributes;
        $slot = $button->slot;
        $replaceHtml = '';
        $hidden = false;

        foreach ($rules as $rule) {
            $forAction = $rule['action'] ?? null;

            $matches = is_array($forAction)
                ? in_array($button->action, $forAction, strict: true)
                : $forAction === $button->action;

            if (! ($rule['apply'] ?? false) || ! $matches) {
                continue;
            }

            if (data_get($rule, 'rule.hide')) {
                $hidden = true;
                break;
            }

            if (! empty($rule['replaceHtml'])) {
                $replaceHtml = $rule['replaceHtml'];
            }

            $setAttr = data_get($rule, 'rule.setAttribute');

            if (is_array($setAttr) && ! empty($setAttr)) {
                /** @var array<string, mixed>|array<int, array<string, mixed>> $setAttr */
                $entries = isset($setAttr['attribute']) ? [$setAttr] : $setAttr;

                foreach ($entries as $entry) {
                    if (is_array($entry) && isset($entry['attribute'], $entry['value'])) {
                        /** @var string $attributeKey */
                        $attributeKey = $entry['attribute'];
                        $attributes[$attributeKey] = $entry['value'];
                    }
                }
            }

            if ($slotOverride = data_get($rule, 'rule.slot')) {
                $slot = $slotOverride;
            }
        }

        if ($hidden) {
            return ['hidden' => true, 'replaceHtml' => ''];
        }

        return [
            'hidden' => false,
            'tag' => $button->tag ?? 'button',
            'slot' => $slot,
            'iconHtml' => ! empty($button->icon) ? $this->renderIcon($button->icon, $button->iconAttributes) : '',
            'attributes' => $attributes,
            'replaceHtml' => $replaceHtml,
        ];
    }

    /** @param  array<string, mixed>  $iconAttributes */
    private function renderIcon(string $icon, array $iconAttributes): string
    {
        $cacheKey = $icon.'::'.md5(serialize($iconAttributes));

        if (! isset($this->iconRenderCache[$cacheKey])) {
            try {
                $this->iconRenderCache[$cacheKey] = Blade::render(
                    '<x-dynamic-component :component="$component" :attributes="new \Illuminate\View\ComponentAttributeBag($attrs)" />',
                    ['component' => $icon, 'attrs' => $iconAttributes],
                );
            } catch (\Throwable) {
                $this->iconRenderCache[$cacheKey] = '';
            }
        }

        return $this->iconRenderCache[$cacheKey];
    }

    /** @return list<array<string, mixed>> */
    public function resolveActionRules(mixed $row): array
    {
        return collect($this->actionRules($row)) // @phpstan-ignore-line
            ->transform(function ($rule) use ($row) {
                $when = data_get($rule, 'rule.when');
                $loop = data_get($rule, 'rule.loop');

                $apply = $when ? $when($row) : false; // @phpstan-ignore-line

                if (data_get($rule, 'rule.loop')) {
                    $apply = $loop ? $loop($row) : false; // @phpstan-ignore-line
                }

                data_forget($rule, 'rule.when');

                if ($bladeComponent = data_get($rule, 'rule.bladeComponent')) {
                    $html = Blade::render(
                        '<x-dynamic-component :component="$component" :attributes="$params" />',
                        [
                            'component' => data_get($bladeComponent, 'component'),
                            'params' => new ComponentAttributeBag(
                                array_merge((array) data_get($bladeComponent, 'params'))
                            ),
                        ],
                    );

                    data_forget($rule, 'rule.bladeComponent');
                }

                return [
                    'action' => data_get($rule, 'forAction'),
                    'apply' => $apply,
                    'column' => $rule->column, // @phpstan-ignore-line
                    'rule' => data_get($rule, 'rule'),
                    'replaceHtml' => $html ?? '',
                ];
            })
            ->all();
    }
}
