<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Blade, Cache};
use Illuminate\View\ComponentAttributeBag;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Support\Actions\ButtonRenderer;
use PowerComponents\Turbine\Button;

trait HasActions
{
    private ?bool $hasTransformActions = null;

    /** @var array<string, list<object>> */
    private array $actionRulesCache = [];

    /**
     * @param  object|array<mixed>  $row
     * @return list<object>
     */
    private function actionRulesForRow(object|array $row): array
    {
        $rawKey = data_get($row, $this->realPrimaryKey());

        $key = is_scalar($rawKey) ? (string) $rawKey : '';

        if ($key !== '' && array_key_exists($key, $this->actionRulesCache)) {
            return $this->actionRulesCache[$key];
        }

        /** @var list<object> $rules */
        $rules = $this->actionRules($row); // @phpstan-ignore-line

        if ($key !== '') {
            $this->actionRulesCache[$key] = $rules;
        }

        return $rules;
    }

    public function shouldCollectActions(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        return $this->hasTransformActions();
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
            $rules = $this->actionRulesForRow($row);

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
                            'toggleDetailView' => theme_view($showToggleDetail ? 'toggle-detail' : 'no-toggle-detail'),
                            ...$pluginModifiers,
                            ...$detailView,
                        ];
                    }

                    return [
                        ...$pluginModifiers,
                        'toggleDetailView' => theme_view($showToggleDetail ? 'toggle-detail' : 'no-toggle-detail'),
                    ];
                })
                ->toArray();
        };

        $value = data_get($row, $this->realPrimaryKey());
        // Rules depend on row content, so an edited row (or one without a key) must not reuse a stale entry.
        $cacheKey = "pg-prepare-action-rules-for-rows-{$this->getId()}-".(is_scalar($value) ? $value : '').'-'.hash('xxh128', (string) json_encode($row));

        /** @var int $cacheTtl */
        $cacheTtl = config('livewire-powergrid.cache_ttl');
        if ($cacheTtl > 0 && filled($value)) {
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
            ->sortBy(fn (Button $button) => $button->order ?? PHP_INT_MAX)
            ->map(fn (Button $button) => ButtonRenderer::resolve($button, $row, []))
            ->filter(fn (array $action) => ! $action['hidden'])
            ->values()
            ->all();

        if (empty($actions)) {
            return '';
        }

        return ButtonRenderer::html($actions);
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
                return ButtonRenderer::resolve($button, $row, $rules);
            })
            ->filter(fn (array $action) => ! $action['hidden'])
            ->values()
            ->all();

        if ($this->hasTransformActions()) {
            $actions = $this->applyTransformActions($row, $actions);
        }

        if (empty($actions)) {
            return '';
        }

        return ButtonRenderer::html($actions);
    }

    private function hasTransformActions(): bool
    {
        return $this->hasTransformActions ??= (new \ReflectionMethod($this, 'transformActions'))
            ->getDeclaringClass()->getName() !== PowerGridComponent::class;
    }

    /**
     * @param  array<int, array<string, mixed>>  $actions
     * @return array<int, array<string, mixed>>
     */
    private function applyTransformActions(object $row, array $actions): array
    {
        /** @var string|int $rowId */
        $rowId = data_get($row, $this->realPrimaryKey());

        /** @var Collection<int, mixed> $rows */
        $rows = collect([$row]);

        $transformed = $this->transformActions([$rowId => array_values($actions)], $rows);

        return array_values($transformed[$rowId] ?? $actions);
    }

    /** @return list<array<string, mixed>> */
    public function resolveActionRules(mixed $row): array
    {
        return collect($this->actionRulesForRow($row)) // @phpstan-ignore-line
            ->transform(function ($rule) use ($row) {
                if (is_object($rule)) {
                    $rule = clone $rule;
                }

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
