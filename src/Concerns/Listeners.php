<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Livewire\Attributes\On;
use ReflectionClass;

/** @codeCoverageIgnore */
trait Listeners
{
    /**
     * @var array<string, bool>
     */
    public array $draftColumns = [];

    /** @var array<string, array{plugin: string, method: string}> */
    private array $pluginListenerMap = [];

    private ?string $lastDispatchedEvent = null;

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

        foreach ($this->pluginListenerMap as $event => $info) {
            $pluginName = $info['plugin'];
            $method = $info['method'];

            if (method_exists($this, $method)) {
                continue;
            }

            if (isset($this->plugins[$pluginName]) && method_exists($this->plugins[$pluginName], $method)) {
                $this->plugins[$pluginName]->{$method}(...$params);

                return;
            }
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
                data_set($column, 'hidden', ! data_get($column, 'hidden'));

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
     * @throws Exception
     */
    public function applyColumns(): void
    {
        foreach ($this->columns as &$column) {
            $field = data_get($column, 'field');

            if (! is_string($field) || data_get($column, 'forceHidden') || ! array_key_exists($field, $this->draftColumns)) {
                continue;
            }

            data_set($column, 'hidden', ! (bool) $this->draftColumns[$field]);
        }
        unset($column);

        $this->persistState('columns');
    }

    /**
     * @throws Exception
     */
    public function resetColumns(): void
    {
        $defaults = collect($this->declaredColumns())
            ->mapWithKeys(function ($column) {
                $field = data_get($column, 'field');

                return [(is_string($field) ? $field : '') => (bool) data_get($column, 'hidden')];
            });

        foreach ($this->columns as &$column) {
            $field = data_get($column, 'field');

            if (! is_string($field) || data_get($column, 'forceHidden') || ! $defaults->has($field)) {
                continue;
            }

            data_set($column, 'hidden', $defaults->get($field));
        }
        unset($column);

        $this->draftColumns = $this->columnVisibilityState();

        $this->persistState('columns');
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
        if (($this->total() > 0) && ($this->totalCurrentPage - 1) === 0) {
            $this->previousPage();

            return;
        }

        $this->dispatch('$commit')->self();
    }
}
