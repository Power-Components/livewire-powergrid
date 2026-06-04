<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Livewire\Attributes\On;
use ReflectionClass;

/** @codeCoverageIgnore */
trait Listeners
{
    /**
     * Maps plugin event names to [pluginName, methodName] for delegation.
     */
    private array $pluginListenerMap = [];

    public function getListeners(): array
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }

        $listeners = [];

        $this->resolvePlugins();

        foreach ($this->plugins as $plugin) {
            $reflection = new ReflectionClass($plugin);
            foreach ($reflection->getMethods() as $method) {
                $attributes = $method->getAttributes(On::class);
                foreach ($attributes as $attribute) {
                    /** @var On $instance */
                    $instance = $attribute->newInstance();
                    $event = str_replace('{tableName}', $this->tableName, $instance->event);

                    $this->pluginListenerMap[$method->getName()] = $plugin->name();
                    $listeners[$event] = $method->getName();
                }
            }
        }

        return $listeners;
    }

    /**
     * Generic delegate for plugin listener methods.
     * Livewire's Wrapped class requires method_exists(), so we override __call
     * in PowerGridComponent. But since Wrapped bypasses __call, we provide
     * explicit delegate methods for known plugin listener patterns.
     */
    public function delegateToPlugin(string $method, array $params): mixed
    {
        $this->resolvePlugins();

        $pluginName = $this->pluginListenerMap[$method] ?? null;

        if ($pluginName && isset($this->plugins[$pluginName])) {
            return $this->plugins[$pluginName]->{$method}(...$params);
        }

        // Fallback: try all plugins
        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, $method)) {
                return $plugin->{$method}(...$params);
            }
        }

        return null;
    }

    /**
     * Proxy for plugin listener: datePickerChanged
     */
    public function datePickerChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('datePickerChanged', $params);
    }

    /**
     * Proxy for plugin listener: inputTextChanged
     */
    public function inputTextChanged(mixed ...$params): void
    {
        $this->delegateToPlugin('inputTextChanged', $params);
    }

    /**
     * Proxy for plugin listener: toggleableChanged
     */
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
