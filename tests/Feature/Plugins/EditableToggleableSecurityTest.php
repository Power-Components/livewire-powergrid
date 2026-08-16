<?php

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;

uses()->group('plugins', 'security');

function editableSecurityComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'editable-security';

        /** @var array<string, mixed> */
        public array $name = [];

        /** @var list<array<string, mixed>> */
        public array $captured = [];

        public function onPluginUpdated(string $plugin, string $event, array $params): void
        {
            $this->captured[] = compact('plugin', 'event', 'params');
        }

        public function columns(): array
        {
            return [
                Column::add()->title('Name')->field('name')->editOnClick(),
            ];
        }
    };
}

function toggleableSecurityComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'toggleable-security';

        /** @var list<array<string, mixed>> */
        public array $captured = [];

        public function onPluginUpdated(string $plugin, string $event, array $params): void
        {
            $this->captured[] = compact('plugin', 'event', 'params');
        }

        public function columns(): array
        {
            return [
                Column::add()->title('In stock')->field('in_stock')->toggleable(),
            ];
        }
    };
}

beforeEach(function () {
    PowerGrid::plugins([
        EditablePlugin::class,
        ToggleablePlugin::class,
    ]);
});

describe('Editable field validation', function () {
    it('applies the change for a field declared with editOnClick()', function () {
        $component = editableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $plugin = $component->getPlugins()['editable'];
        $plugin->inputTextChanged('name', 1, 'Updated');

        expect($component->name)->toBe(['1' => 'Updated'])
            ->and($component->captured)->toHaveCount(1)
            ->and($component->captured[0]['plugin'])->toBe('editable')
            ->and($component->captured[0]['params'])->toBe(['id' => '1', 'field' => 'name', 'value' => 'Updated']);
    });

    it('ignores a field that was not declared editable', function () {
        $component = editableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $plugin = $component->getPlugins()['editable'];
        $plugin->inputTextChanged('undeclared_field', 1, 'Updated');

        expect($component->captured)->toBeEmpty()
            ->and(isset($component->undeclared_field))->toBeFalse();
    });
});

describe('Toggleable field validation', function () {
    it('applies the change for a field declared toggleable()', function () {
        $component = toggleableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $plugin = $component->getPlugins()['toggleable'];
        $plugin->toggleableChanged('in_stock', 1, '1');

        expect($component->captured)->toHaveCount(1)
            ->and($component->captured[0]['plugin'])->toBe('toggleable')
            ->and($component->captured[0]['params'])->toBe(['id' => '1', 'field' => 'in_stock', 'value' => '1']);
    });

    it('ignores a field that was not declared toggleable', function () {
        $component = toggleableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $plugin = $component->getPlugins()['toggleable'];
        $plugin->toggleableChanged('undeclared_field', 1, '1');

        expect($component->captured)->toBeEmpty();
    });

    it('ignores a non-string field argument', function () {
        $component = toggleableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $plugin = $component->getPlugins()['toggleable'];
        $plugin->toggleableChanged(['array'], 1, '1');

        expect($component->captured)->toBeEmpty();
    });
});
