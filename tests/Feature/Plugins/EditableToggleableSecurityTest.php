<?php

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\Facades\{PowerGrid, Rule};
use PowerComponents\LivewirePowerGrid\Plugins\Editable\EditablePlugin;
use PowerComponents\LivewirePowerGrid\Plugins\Toggleable\ToggleablePlugin;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

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

        public function datasource(): Collection
        {
            return collect([['id' => 1, 'name' => 'Pizza', 'in_stock' => 1, 'locked' => false], ['id' => 2, 'name' => 'Pasta', 'in_stock' => 0, 'locked' => true]]);
        }

        public function transformQuery(mixed $query): mixed
        {
            return $query->where('id', '!=', 3);
        }

        public function actionRules(mixed $row): array
        {
            return [
                Rule::editOnClick('name')->when(fn ($row) => data_get($row, 'locked'))->disable(),
                Rule::toggleable('in_stock')->when(fn ($row) => data_get($row, 'locked'))->hide(),
            ];
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

        public function datasource(): Collection
        {
            return collect([['id' => 1, 'name' => 'Pizza', 'in_stock' => 1, 'locked' => false], ['id' => 2, 'name' => 'Pasta', 'in_stock' => 0, 'locked' => true]]);
        }

        public function transformQuery(mixed $query): mixed
        {
            return $query->where('id', '!=', 3);
        }

        public function actionRules(mixed $row): array
        {
            return [
                Rule::editOnClick('name')->when(fn ($row) => data_get($row, 'locked'))->disable(),
                Rule::toggleable('in_stock')->when(fn ($row) => data_get($row, 'locked'))->hide(),
            ];
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

describe('Editable and toggleable row scope', function () {
    it('ignores an editable id outside the scoped datasource', function (mixed $id) {
        $component = editableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $component->getPlugins()['editable']->inputTextChanged('name', $id, 'Updated');

        expect($component->captured)->toBeEmpty()
            ->and($component->name)->toBe([]);
    })->with([
        'unknown id' => [99],
        'id removed by transformQuery' => [3],
        'wildcard' => ['*'],
        'dotted path' => ['1.foo'],
    ]);

    it('ignores a non-scalar editable value', function () {
        $component = editableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $component->getPlugins()['editable']->inputTextChanged('name', 1, ['nested' => 'x']);

        expect($component->captured)->toBeEmpty();
    });

    it('honours a row rule that hides editOnClick', function () {
        $component = editableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $component->getPlugins()['editable']->inputTextChanged('name', 2, 'Updated');

        expect($component->captured)->toBeEmpty();
    });

    it('ignores a toggleable value other than 0 or 1', function (mixed $value) {
        $component = toggleableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $component->getPlugins()['toggleable']->toggleableChanged('in_stock', 1, $value);

        expect($component->captured)->toBeEmpty();
    })->with(['5', 'yes', [['1']]]);

    it('ignores a toggleable id outside the scope or hidden by a rule', function (int $id) {
        $component = toggleableSecurityComponent();
        $component->columns = $component->columns();
        $component->resolvePlugins();

        $component->getPlugins()['toggleable']->toggleableChanged('in_stock', $id, '1');

        expect($component->captured)->toBeEmpty();
    })->with([99, 2]);
});

it('scopes editable ids against a database datasource and transformQuery', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'editable-db-scope';

        /** @var array<string, mixed> */
        public array $name = [];

        /** @var list<array<string, mixed>> */
        public array $captured = [];

        public function datasource()
        {
            return Dish::query();
        }

        public function transformQuery(mixed $query): mixed
        {
            return $query->where('name', 'like', 'Pizza%');
        }

        public function onPluginUpdated(string $plugin, string $event, array $params): void
        {
            $this->captured[] = $params['id'];
        }

        public function columns(): array
        {
            return [Column::add()->title('Name')->field('name')->editOnClick()];
        }
    };

    $component->columns = $component->columns();
    $component->resolvePlugins();

    Dish::query()->forceDelete();
    [$pizza, $other] = collect(['Pizza Margherita', 'Sushi'])
        ->map(fn ($name) => Dish::create(['name' => $name, 'price' => 10, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now()])->id)
        ->all();

    $component->getPlugins()['editable']->inputTextChanged('name', $other, 'Hacked');
    $component->getPlugins()['editable']->inputTextChanged('name', $pizza, 'Updated');

    expect($component->captured)->toBe([(string) $pizza]);
});

it('does not reuse cached action rules after the row content changes', function () {
    config()->set('livewire-powergrid.cache_ttl', 60);

    $component = editableSecurityComponent();
    $component->columns = $component->columns();
    $component->resolvePlugins();

    $hidden = fn (array $row) => collect($component->prepareActionRulesForRows((object) $row))
        ->contains(fn ($rule) => (bool) data_get($rule, 'fieldHideEditOnClick'));

    expect($hidden(['id' => 1, 'locked' => true]))->toBeTrue()
        ->and($hidden(['id' => 1, 'locked' => false]))->toBeFalse();
});
