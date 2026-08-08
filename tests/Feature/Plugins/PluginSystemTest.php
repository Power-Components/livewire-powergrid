<?php

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridManager};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Export\ExportPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable;

beforeEach(function () {
    $this->defaultPlugins = [
        EditablePlugin::class,
        ToggleablePlugin::class,
        FlatpickrPlugin::class,
    ];
});

afterEach(function () {
    PowerGrid::plugins($this->defaultPlugins);
});

it('verifies that plugins are registered globally through the facade', function () {
    $component = new DishesTable();

    $customPlugin = new class($component) extends PluginBase
    {
        public function name(): string
        {
            return 'custom-plugin';
        }

        public function isEnabled(): bool
        {
            return true;
        }
    };

    PowerGrid::plugins([
        get_class($customPlugin),
    ]);

    expect(PowerGridManager::$plugins)->toContain(get_class($customPlugin));
});

it('keeps built-in plugins when registering custom plugins', function () {
    $component = new DishesTable();

    $customPlugin = new class($component) extends PluginBase
    {
        public function name(): string
        {
            return 'custom-plugin';
        }

        public function isEnabled(): bool
        {
            return true;
        }
    };

    PowerGrid::plugins([get_class($customPlugin)]);

    expect(PowerGridManager::$plugins)
        ->toContain(get_class($customPlugin))
        ->toContain(ExportPlugin::class)
        ->toContain(EditablePlugin::class)
        ->toContain(ToggleablePlugin::class)
        ->toContain(FlatpickrPlugin::class);

    // de-duplicated: registering a built-in again does not duplicate it
    PowerGrid::plugins([EditablePlugin::class, get_class($customPlugin)]);

    expect(array_count_values(PowerGridManager::$plugins)[EditablePlugin::class])->toBe(1);
});

it('validates that the core component resolves active plugins', function () {
    PowerGrid::plugins($this->defaultPlugins);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'plugin-system-resolve';

        public function columns(): array
        {
            return [
                Column::add()->field('name')->editOnClick(),
                Column::add()->field('in_stock')->toggleable(),
            ];
        }
    };

    $component->resolvePlugins();

    expect($component->getPlugins())->toHaveKeys(['editable', 'toggleable']);
});

it('verifies that the plugin handles and renders column content', function () {
    PowerGrid::plugins($this->defaultPlugins);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'plugin-system-render';

        public function columns(): array
        {
            return [
                Column::add()->field('name')->editOnClick(),
            ];
        }
    };

    $component->resolvePlugins();

    $column = Column::add()->field('name')->editOnClick();
    $row = (object) ['name' => 'Dish Name', 'id' => 1];

    $content = $component->renderColumnContent($column, $row);

    expect($content)
        ->toContain('pgEditable')
        ->toContain('Dish Name');
});

it('verifies that toggleable plugin handles and renders column content', function () {
    PowerGrid::plugins($this->defaultPlugins);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'plugin-system-toggleable';

        public function columns(): array
        {
            return [
                Column::add()->field('in_stock')->toggleable(),
            ];
        }
    };

    $component->resolvePlugins();

    $column = Column::add()->field('in_stock')->toggleable();
    $row = (object) ['in_stock' => true, 'id' => 1];

    $content = $component->renderColumnContent($column, $row);

    expect($content)->toContain('pgToggleable');
});

it('verifies that flatpickr plugin is enabled when date filters are present', function () {
    PowerGrid::plugins($this->defaultPlugins);
    $component = new DishesTable();

    $column = Column::add()->field('produced_at');
    $column->filters = [
        ['type' => 'date_picker'],
    ];

    $component->columns = [$column];
    $component->resolvePlugins();

    expect($component->getPlugins())->toHaveKey('flatpickr');
});
