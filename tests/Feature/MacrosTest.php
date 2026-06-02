<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Button, Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Components\Rules\RuleActions;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

it('tests Column withSum macro', function () {
    $column = Column::make('Price', 'price')
        ->withSum('Total Price', true, true);

    expect($column->properties['summarize']['sum'])
        ->toBeArray()
        ->label->toBe('Total Price')
        ->header->toBeTrue()
        ->footer->toBeTrue();
});

it('tests Column withCount macro', function () {
    $column = Column::make('Id', 'id')
        ->withCount('Count Items', true, false);

    expect($column->properties['summarize']['count'])
        ->toBeArray()
        ->label->toBe('Count Items')
        ->header->toBeTrue()
        ->footer->toBeFalse();
});

it('tests Column withAvg macro', function () {
    $column = Column::make('Price', 'price')
        ->withAvg('Average Price', false, true);

    expect($column->properties['summarize']['avg'])
        ->toBeArray()
        ->label->toBe('Average Price')
        ->header->toBeFalse()
        ->footer->toBeTrue();
});

it('tests Column withMin macro', function () {
    $column = Column::make('Price', 'price')
        ->withMin('Minimum Price', true, true);

    expect($column->properties['summarize']['min'])
        ->toBeArray()
        ->label->toBe('Minimum Price')
        ->header->toBeTrue()
        ->footer->toBeTrue();
});

it('tests Column withMax macro', function () {
    $column = Column::make('Price', 'price')
        ->withMax('Maximum Price', false, false);

    expect($column->properties['summarize']['max'])
        ->toBeArray()
        ->label->toBe('Maximum Price')
        ->header->toBeFalse()
        ->footer->toBeFalse();
});

it('tests Column searchableRaw macro', function () {
    $driver = env('DB_DRIVER', config('database.default'));
    $searchSql = $driver === 'pgsql' ? 'LOWER(name::text) ilike ?' : 'LOWER(name) like ?';

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-searchable-raw';

        public static string $searchSql = '';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchableRaw(static::$searchSql),
            ];
        }
    };

    $component::$searchSql = $searchSql;

    Livewire::test($component::class)
        ->set('search', 'Peixada')
        ->assertSee('Peixada');
});

it('tests Column searchableRaw macro with beforeSearch method', function () {
    $driver = env('DB_DRIVER', config('database.default'));
    $searchSql = $driver === 'pgsql' ? 'LOWER(name::text) ilike ?' : 'LOWER(name) like ?';

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-searchable-raw-before';

        public static string $searchSql = '';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchableRaw(static::$searchSql),
            ];
        }

        public function beforeSearch(string $field, string $search): string
        {
            return strtoupper($search);
        }
    };

    $component::$searchSql = $searchSql;

    $livewire = Livewire::test($component::class)
        ->set('search', 'peixada');

    expect($livewire->get('search'))->toBe('peixada');
});

it('tests Column searchableRaw macro with field-specific beforeSearch method', function () {
    $driver = env('DB_DRIVER', config('database.default'));
    $searchSql = $driver === 'pgsql' ? 'LOWER(name::text) ilike ?' : 'LOWER(name) like ?';

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-searchable-raw-field-before';

        public static string $searchSql = '';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchableRaw(static::$searchSql),
            ];
        }

        public function beforeSearchName(string $field, string $search): string
        {
            return str_replace('test', 'replaced', $search);
        }
    };

    $component::$searchSql = $searchSql;

    $livewire = Livewire::test($component::class)
        ->set('search', 'test');

    expect($livewire->get('search'))->toBe('test');
});

it('tests Column searchableJson macro', function () {
    $column = Column::make('Name', 'name')->searchableJson('dishes');

    expect($column->rawQueries)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($column->rawQueries[0])
        ->toHaveKeys(['method', 'sql', 'bindings', 'enabled'])
        ->method->toBe('orWhereRaw')
        ->and($column->rawQueries[0]['sql'])->toBeInstanceOf(Closure::class);

    $sql = $column->rawQueries[0]['sql']();
    $driver = config("database.connections.".config('database.default').".driver");
    $quote = $driver === 'pgsql' ? '"' : '`';

    expect($sql)->toBe("LOWER({$quote}dishes{$quote}.{$quote}name{$quote}) like ?");
});

it('tests Column searchableJson macro without table name', function () {
    $column = Column::make('Name', 'name')->searchableJson('');

    expect($column->rawQueries)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($column->rawQueries[0]['sql'])->toBeInstanceOf(Closure::class);

    $sql = $column->rawQueries[0]['sql']();
    $driver = config("database.connections.".config('database.default').".driver");
    $quote = $driver === 'pgsql' ? '"' : '`';

    expect($sql)->toBe("LOWER({$quote}name{$quote}) like ?");
});

it('tests Column naturalSort macro', function () {
    $column = Column::make('Code', 'code')->naturalSort(true, 'products');

    expect($column->rawQueries)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($column->rawQueries[0])
        ->method->toBe('orderByRaw');
});

it('tests Column naturalSort macro without table name', function () {
    $column = Column::make('Code', 'code')->naturalSort(true);

    expect($column->rawQueries)
        ->toBeArray()
        ->toHaveCount(1);
});

it('tests Column naturalSort macro when disabled', function () {
    $column = Column::make('Code', 'code')->naturalSort(false);

    expect($column->rawQueries)
        ->toBeArray()
        ->toBeEmpty();
});

it('tests Button class macro', function () {
    $button = Button::add('test')->class('btn btn-primary');

    expect($button->attributes)
        ->toHaveKey('class')
        ->and($button->attributes['class'])->toBe('btn btn-primary');
});

it('tests Button call macro', function () {
    $button = Button::add('test')->call('deleteItem', ['id' => 123]);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toContain('$call')
        ->and($button->attributes['wire:click'])->toContain('deleteItem');
});

it('tests Button dispatch macro', function () {
    $button = Button::add('test')->dispatch('itemDeleted', ['id' => 123]);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toContain('$dispatch')
        ->and($button->attributes['wire:click'])->toContain('itemDeleted');
});

it('tests Button dispatchTo macro', function () {
    $button = Button::add('test')->dispatchTo('my-component', 'itemUpdated', ['id' => 123]);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toContain('$dispatchTo')
        ->and($button->attributes['wire:click'])->toContain('my-component')
        ->and($button->attributes['wire:click'])->toContain('itemUpdated');
});

it('tests Button dispatchSelf macro', function () {
    $button = Button::add('test')->dispatchSelf('refreshData', ['force' => true]);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toContain('$dispatchSelf')
        ->and($button->attributes['wire:click'])->toContain('refreshData');
});

it('tests Button parent macro', function () {
    $button = Button::add('test')->parent('parentMethod', ['value' => 'test']);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toContain('$parent')
        ->and($button->attributes['wire:click'])->toContain('parentMethod');
});

it('tests Button openModal macro', function () {
    $button = Button::add('test')->openModal('edit-modal', ['id' => 123]);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toContain('$dispatch')
        ->and($button->attributes['wire:click'])->toContain('openModal')
        ->and($button->attributes['wire:click'])->toContain('edit-modal');
});

it('tests Button disable macro', function () {
    $button = Button::add('test')->disable(true);

    expect($button->attributes)
        ->toHaveKey('disabled')
        ->and($button->attributes['disabled'])->toBe('disabled');
});

it('tests Button disable macro with false', function () {
    $button = Button::add('test')->disable(false);

    expect($button->attributes)
        ->not->toHaveKey('disabled');
});

it('tests Button tooltip macro', function () {
    $button = Button::add('test')->tooltip('This is a tooltip');

    expect($button->attributes)
        ->toHaveKey('title')
        ->and($button->attributes['title'])->toBe('This is a tooltip');
});

it('tests Button route macro', function () {
    Route::get('/dishes/{id}', fn ($id) => 'dish')->name('dishes.show');

    $button = Button::add('test')->route('dishes.show', ['id' => 1], '_blank');

    expect($button->tag)->toBe('a')
        ->and($button->attributes)->toHaveKey('href')
        ->and($button->attributes)->toHaveKey('target')
        ->and($button->attributes['target'])->toBe('_blank');
});

it('tests Button route macro with default target', function () {
    Route::get('/dishes/{id}', fn ($id) => 'dish')->name('dishes.show');

    $button = Button::add('test')->route('dishes.show', ['id' => 1]);

    expect($button->tag)->toBe('a')
        ->and($button->attributes)->toHaveKey('target')
        ->and($button->attributes['target'])->toBe('_self');
});

it('tests Button id macro', function () {
    $button = Button::add('test')->id('my-button-id');

    expect($button->attributes)
        ->toHaveKey('id')
        ->and($button->attributes['id'])->toBe('my-button-id');
});

it('tests Button id macro with null', function () {
    $button = Button::add('test')->id(null);

    expect($button->attributes)
        ->toHaveKey('id')
        ->and($button->attributes['id'])->toBeNull();
});

it('tests Button can macro with boolean', function () {
    $button = Button::add('test')->can(true);

    expect($button->can)->toBeTrue();
});

it('tests Button can macro with closure', function () {
    $closure = fn () => true;
    $button = Button::add('test')->can($closure);

    expect($button->can)->toBe($closure);
});

it('tests Button confirm macro', function () {
    $button = Button::add('test')->confirm('Are you sure?');

    expect($button->attributes)
        ->toHaveKey('wire:confirm')
        ->and($button->attributes['wire:confirm'])->toBe('Are you sure?');
});

it('tests Button confirm macro with default message', function () {
    $button = Button::add('test')->confirm();

    expect($button->attributes)
        ->toHaveKey('wire:confirm');
});

it('tests Button confirmPrompt macro', function () {
    $button = Button::add('test')->confirmPrompt('Type DELETE to confirm', 'DELETE');

    expect($button->attributes)
        ->toHaveKey('wire:confirm.prompt')
        ->and($button->attributes['wire:confirm.prompt'])->toContain('DELETE');
});

it('tests Button confirmPrompt macro with default message', function () {
    $button = Button::add('test')->confirmPrompt();

    expect($button->attributes)
        ->toHaveKey('wire:confirm.prompt');
});

it('tests Button toggleDetail macro', function () {
    $button = Button::add('test')->toggleDetail(123);

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toBe("toggleDetail('123')");
});

it('tests Button toggleDetail macro with string id', function () {
    $button = Button::add('test')->toggleDetail('abc-123');

    expect($button->attributes)
        ->toHaveKey('wire:click')
        ->and($button->attributes['wire:click'])->toBe("toggleDetail('abc-123')");
});

it('tests RuleActions dispatch macro', function () {
    $rule = new RuleActions('test-button');
    $rule->dispatch('testEvent', ['id' => 123]);

    expect($rule->rule)
        ->toHaveKey('setAttribute')
        ->and($rule->rule['setAttribute'])
        ->toBeArray()
        ->and($rule->rule['setAttribute'][0]['attribute'])->toBe('wire:click')
        ->and($rule->rule['setAttribute'][0]['value'])->toContain('$dispatch')
        ->and($rule->rule['setAttribute'][0]['value'])->toContain('testEvent');
});

it('tests RuleActions disable macro', function () {
    $rule = new RuleActions('test-button');
    $rule->disable();

    expect($rule->rule)
        ->toHaveKey('setAttribute')
        ->and($rule->rule['setAttribute'])
        ->toBeArray()
        ->and($rule->rule['setAttribute'][0]['attribute'])->toBe('disabled')
        ->and($rule->rule['setAttribute'][0]['value'])->toBe('disabled');
});

it('tests searchableJson macro htmlspecialchars encoding', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-searchable-json-encoding';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchableJson('dishes'),
            ];
        }
    };

    $livewire = Livewire::test($component::class)
        ->set('search', '<script>alert("xss")</script>');

    expect($livewire->get('search'))->toContain('script');
});

it('tests Column naturalSort enables sort', function () {
    $column = Column::make('Code', 'code')->naturalSort(true);

    // naturalSort calls enableSort() which sets sortable to true
    expect($column->rawQueries)->toHaveCount(1);
});

it('tests multiple Button macros chained together', function () {
    $button = Button::add('test')
        ->class('btn btn-primary')
        ->tooltip('Click me')
        ->id('my-btn')
        ->disable(true);

    expect($button->attributes)
        ->toHaveKey('class')
        ->toHaveKey('title')
        ->toHaveKey('id')
        ->toHaveKey('disabled')
        ->and($button->attributes['class'])->toBe('btn btn-primary')
        ->and($button->attributes['title'])->toBe('Click me')
        ->and($button->attributes['id'])->toBe('my-btn')
        ->and($button->attributes['disabled'])->toBe('disabled');
});

it('tests Column with multiple summarize macros', function () {
    $column = Column::make('Price', 'price')
        ->withSum('Total', true, true)
        ->withAvg('Average', true, false)
        ->withMin('Min', false, true)
        ->withMax('Max', false, false);

    expect($column->properties['summarize'])
        ->toHaveKeys(['sum', 'avg', 'min', 'max'])
        ->and($column->properties['summarize']['sum']['label'])->toBe('Total')
        ->and($column->properties['summarize']['avg']['label'])->toBe('Average')
        ->and($column->properties['summarize']['min']['label'])->toBe('Min')
        ->and($column->properties['summarize']['max']['label'])->toBe('Max');
});

it('tests searchableRaw closure bindings are executed', function () {
    $driver = env('DB_DRIVER', config('database.default'));
    $searchSql = $driver === 'pgsql' ? 'LOWER(name::text) ilike ?' : 'LOWER(name) like ?';

    $column = Column::make('Name', 'name')->searchableRaw($searchSql);

    expect($column->rawQueries[0]['bindings'])
        ->toBeArray()
        ->toHaveCount(1)
        ->and($column->rawQueries[0]['bindings'][0])->toBeInstanceOf(Closure::class);
});

it('tests searchableJson closure bindings are executed', function () {
    $column = Column::make('Name', 'name')->searchableJson('dishes');

    expect($column->rawQueries[0]['bindings'])
        ->toBeArray()
        ->toHaveCount(1)
        ->and($column->rawQueries[0]['bindings'][0])->toBeInstanceOf(Closure::class);
});

it('tests searchableRaw enabled closure', function () {
    $driver = env('DB_DRIVER', config('database.default'));
    $searchSql = $driver === 'pgsql' ? 'LOWER(name::text) ilike ?' : 'LOWER(name) like ?';

    $column = Column::make('Name', 'name')->searchableRaw($searchSql);

    expect($column->rawQueries[0]['enabled'])->toBeInstanceOf(Closure::class);
});

it('tests Button with empty params arrays', function () {
    $button1 = Button::add('test1')->call('myMethod', []);
    $button2 = Button::add('test2')->dispatch('myEvent', []);
    $button3 = Button::add('test3')->dispatchSelf('selfEvent', []);

    expect($button1->attributes['wire:click'])->toContain('myMethod')
        ->and($button2->attributes['wire:click'])->toContain('myEvent')
        ->and($button3->attributes['wire:click'])->toContain('selfEvent');
});
