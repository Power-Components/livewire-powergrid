<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Livewire\Attributes\On;
use ReflectionClass;

/** @codeCoverageIgnore */
trait Listeners
{
    /** @var array<string, array{plugin: string, method: string}> */
    private array $pluginListenerMap = [];

    protected mixed $dispatchingEvent = null;

    /** @var array<class-string, list<array{event: string, method: string}>> */
    private static array $reflectionCache = [];

    /** @return array<string, string> */
    public function getListeners(): array
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }

        $listeners = [];

        $this->resolvePlugins();

        foreach ($this->plugins as $plugin) {
            $pluginClass = get_class($plugin);

            if (! isset(self::$reflectionCache[$pluginClass])) {
                $reflection = new ReflectionClass($plugin);
                $methods = [];
                foreach ($reflection->getMethods() as $method) {
                    $attributes = $method->getAttributes(On::class);
                    foreach ($attributes as $attribute) {
                        $methods[] = [
                            'event' => $attribute->newInstance()->event,
                            'method' => $method->getName(),
                        ];
                    }
                }
                self::$reflectionCache[$pluginClass] = $methods;
            }

            foreach (self::$reflectionCache[$pluginClass] as $entry) {
                $event = str_replace('{tableName}', $this->tableName, $entry['event']);

                $this->pluginListenerMap[$event] = [
                    'plugin' => $plugin->name(),
                    'method' => $entry['method'],
                ];

                if (method_exists($this, $entry['method'])) {
                    $listeners[$event] = $entry['method'];
                } else {
                    $listeners[$event] = 'pgPluginListener';
                }
            }
        }

        return $listeners;
    }

    public function pgPluginListener(mixed ...$params): void
    {
        $this->resolvePlugins();

        $info = is_string($this->dispatchingEvent) ? ($this->pluginListenerMap[$this->dispatchingEvent] ?? null) : null;

        if ($info !== null && isset($this->plugins[$info['plugin']]) && method_exists($this->plugins[$info['plugin']], $info['method'])) {
            $this->plugins[$info['plugin']]->{$info['method']}(...$params);
        }
    }

    /** @param  array<int, mixed>  $params */
    public function delegateToPlugin(string $method, array $params): mixed
    {
        $this->resolvePlugins();

        foreach ($this->pluginListenerMap as $event => $info) {
            if ($info['method'] === $method && isset($this->plugins[$info['plugin']])) {
                return $this->plugins[$info['plugin']]->{$method}(...$params);
            }
        }

        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, $method)) {
                return $plugin->{$method}(...$params);
            }
        }

        return null;
    }

    public function datePickerChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('datePickerChanged', array_values($params));
    }

    public function inputTextChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('inputTextChanged', array_values($params));
    }

    public function toggleableChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('toggleableChanged', array_values($params));
    }

    /**
     * @throws Exception
     */
    #[On('pg:toggleColumn-{tableName}')]
    public function toggleColumn(string $field): void
    {
        foreach ($this->columns as &$column) {
            if (data_get($column, 'field') === $field) {
                if (! data_get($column, 'forceHidden')) {
                    data_set($column, 'hidden', ! data_get($column, 'hidden'));
                }

                break;
            }
        }

        $this->persistState('columns');
    }

    /**
     * @return array<string, bool>
     */
    public function columnVisibilityState(): array
    {
        $state = [];

        foreach ($this->columns as $column) {
            $field = data_get($column, 'field');

            if (! is_string($field) || $field === '' || data_get($column, 'isAction') || data_get($column, 'forceHidden')) {
                continue;
            }

            $state[$field] = ! (bool) data_get($column, 'hidden');
        }

        return $state;
    }

    /**
     * Applies the toggle-columns panel draft; the client already updated the DOM, so nothing is rendered or queried.
     *
     * @param  array<array-key, mixed>  $columns  ordered list of ['field' => string, 'hidden' => bool]
     *
     * @throws Exception
     */
    public function applyColumns(array $columns): void
    {
        $draft = [];

        foreach ($columns as $column) {
            $field = data_get($column, 'field');

            if (is_string($field) && $field !== '') {
                $draft[$field] = (bool) data_get($column, 'hidden');
            }
        }

        $this->arrangeColumns($draft);
        $this->persistState('columns');

        // power-components/partials ignores skipRender(); only a registered fragment suppresses the full render.
        if (function_exists('partials')) {
            partials($this)->partial("pg-toggle-columns-{$this->tableName}", '<span></span>');
        }

        $this->skipRender();
    }

    /**
     * Restores the declared order and visibility.
     *
     * @throws Exception
     */
    public function resetColumns(): void
    {
        $defaults = [];

        foreach ($this->declaredColumns() as $column) {
            $field = data_get($column, 'field');

            if (is_string($field) && $field !== '') {
                $defaults[$field] = (bool) data_get($column, 'hidden');
            }
        }

        $this->arrangeColumns($defaults);
        $this->persistState('columns');
    }

    /** @param  array<string, bool>  $draft  field => hidden, in the wanted order */
    private function arrangeColumns(array $draft): void
    {
        $fields = [];

        foreach ($this->columns as $index => $column) {
            $field = data_get($column, 'field');

            if (! is_string($field) || ! array_key_exists($field, $draft) || data_get($column, 'isAction') || data_get($column, 'forceHidden')) {
                continue;
            }

            data_set($column, 'hidden', $draft[$field]);
            $this->columns[$index] = $column;
            $fields[$field] = true;
        }

        $this->reorderColumns(array_keys(array_intersect_key($draft, $fields)));

        unset($this->visibleColumns, $this->columnViewModels, $this->hasColumnFilters);
    }

    public function hiddenColumnsCount(): int
    {
        return collect($this->columns)
            ->filter(fn ($column) => ! data_get($column, 'isAction')
                && ! data_get($column, 'forceHidden')
                && data_get($column, 'hidden'))
            ->count();
    }

    #[On('pg:eventRefresh-{tableName}')]
    public function refresh(): void
    {
        $this->dispatch('$commit')->self();
    }
}
