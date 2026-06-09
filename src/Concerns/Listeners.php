<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Livewire\Attributes\On;
use ReflectionClass;

/** @codeCoverageIgnore */
trait Listeners
{
    private array $pluginListenerMap = [];

    private ?string $lastDispatchedEvent = null;

    private static array $reflectionCache = [];

    public function getListeners(): array
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }

        $listeners = [];

        $this->resolvePlugins();

        foreach ($this->plugins as $plugin) {
            $pluginClass = get_class($plugin);

            if (! isset(static::$reflectionCache[$pluginClass])) {
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
                static::$reflectionCache[$pluginClass] = $methods;
            }

            foreach (static::$reflectionCache[$pluginClass] as $entry) {
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
        $this->delegateToPlugin('datePickerChanged', $params);
    }

    public function inputTextChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('inputTextChanged', $params);
    }

    public function toggleableChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('toggleableChanged', $params);
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
