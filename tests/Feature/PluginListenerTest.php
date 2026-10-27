<?php

use Livewire\Attributes\On;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridManager};

class FirstListenerPlugin extends PluginBase
{
    public static array $calls = [];

    public function name(): string
    {
        return 'first-listener';
    }

    public function isEnabled(): bool
    {
        return true;
    }

    #[On('first-event')]
    public function onFirstEvent(): void
    {
        static::$calls[] = 'first';
    }
}

class SecondListenerPlugin extends FirstListenerPlugin
{
    public function name(): string
    {
        return 'second-listener';
    }

    #[On('second-event')]
    public function onSecondEvent(): void
    {
        static::$calls[] = 'second';
    }
}

afterEach(fn () => PowerGridManager::$plugins = PowerGridManager::DEFAULT_PLUGINS);

it('runs only the plugin handler bound to the dispatched event', function () {
    PowerGridManager::plugins([FirstListenerPlugin::class, SecondListenerPlugin::class]);
    FirstListenerPlugin::$calls = [];

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-plugin-listener';

        public function datasource()
        {
            return collect([]);
        }
    };

    Livewire::test($component::class)->dispatch('second-event');

    expect(FirstListenerPlugin::$calls)->toBe(['second']);
});
