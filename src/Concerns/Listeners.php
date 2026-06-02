<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use Livewire\Attributes\On;
use ReflectionClass;

/** @codeCoverageIgnore */
trait Listeners
{
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

                    $listeners[$event] = $method->getName();
                }
            }
        }

        return $listeners;
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

    /**
     * Delegate to FlatpickrPlugin
     */
    public function datePickerChanged(mixed ...$params): void
    {
        $this->resolvePlugins();

        if (isset($this->plugins['flatpickr']) && method_exists($this->plugins['flatpickr'], 'datePickerChanged')) {
            $this->plugins['flatpickr']->datePickerChanged(...$params);
        }
    }

    /**
     * Delegate to EditablePlugin
     */
    public function inputTextChanged(mixed ...$params): void
    {
        $this->resolvePlugins();

        if (isset($this->plugins['editable']) && method_exists($this->plugins['editable'], 'inputTextChanged')) {
            $this->plugins['editable']->inputTextChanged(...$params);
        }
    }

    /**
     * Delegate to ToggleablePlugin
     */
    public function toggleableChanged(mixed ...$params): void
    {
        $this->resolvePlugins();

        if (isset($this->plugins['toggleable']) && method_exists($this->plugins['toggleable'], 'toggleableChanged')) {
            $this->plugins['toggleable']->toggleableChanged(...$params);
        }
    }
}
