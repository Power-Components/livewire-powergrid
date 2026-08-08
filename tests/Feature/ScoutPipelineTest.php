<?php

use Illuminate\Support\Facades\Config;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines\{Filters, Search, Sorting};
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

beforeEach(function () {
    Config::set('livewire-powergrid.filter', 'outside');
});

describe('Scout Search Pipeline', function () {
    it('should add search query to builder when search is provided', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $search = 'test search';
        };

        $builder = new ScoutBuilder(new Dish(), 'initial query');
        $pipeline = new Search($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->query)->toBe('test search,initial query');
    });

    it('should not modify query when search is blank', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $search = '';
        };

        $builder = new ScoutBuilder(new Dish(), 'original query');
        $pipeline = new Search($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->query)->toBe('original query');
    });

    it('should handle null search value', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $search = '';
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Search($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->query)->toBe('');
    });

    it('should prepend search to existing query', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $search = 'pizza';
        };

        $builder = new ScoutBuilder(new Dish(), 'pasta');
        $pipeline = new Search($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->query)->toBe('pizza,pasta');
    });
});

describe('Scout Filters Pipeline', function () {
    it('should apply filters to builder when filters are provided', function () {
        $component = new class() extends PowerGridComponent
        {
            public array $filters = [
                'select' => [
                    'category_id' => '1',
                    'status' => 'active',
                ],
            ];

            public function filters(): array
            {
                return [
                    Filter::select('category_id'),
                    Filter::select('status'),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Filters($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->wheres)->toHaveCount(2)
            ->and($result->wheres[0])->toBe(['field' => 'category_id', 'operator' => '=', 'value' => '1'])
            ->and($result->wheres[1])->toBe(['field' => 'status', 'operator' => '=', 'value' => 'active']);
    });

    it('should not modify builder when filters are empty', function () {
        $component = new class() extends PowerGridComponent
        {
            public array $filters = [];
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Filters($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->wheres)->toBeEmpty();
    });

    it('should handle multiple filter types', function () {
        $component = new class() extends PowerGridComponent
        {
            public array $filters = [
                'select' => ['category_id' => '1'],
                'input_text' => ['name' => 'Pizza'],
                'number' => ['price' => '10'],
            ];

            public function filters(): array
            {
                return [
                    Filter::select('category_id'),
                    Filter::inputText('name'),
                    Filter::number('price'),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Filters($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->wheres)->toHaveCount(3)
            ->and($result->wheres[0])->toBe(['field' => 'category_id', 'operator' => '=', 'value' => '1'])
            ->and($result->wheres[1])->toBe(['field' => 'name', 'operator' => '=', 'value' => 'Pizza'])
            ->and($result->wheres[2])->toBe(['field' => 'price', 'operator' => '=', 'value' => '10']);
    });

    it('should handle nested filter arrays', function () {
        $component = new class() extends PowerGridComponent
        {
            public array $filters = [
                'select' => [
                    'field1' => 'value1',
                ],
                'input_text' => [
                    'field2' => 'value2',
                ],
            ];

            public function filters(): array
            {
                return [
                    Filter::select('field1'),
                    Filter::inputText('field2'),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Filters($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->wheres)->toHaveCount(2)
            ->and($result->wheres[0])->toBe(['field' => 'field1', 'operator' => '=', 'value' => 'value1'])
            ->and($result->wheres[1])->toBe(['field' => 'field2', 'operator' => '=', 'value' => 'value2']);
    });
});

describe('Scout Sorting Pipeline', function () {
    it('should apply single sort to builder', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = 'name';

            public string $sortDirection = 'asc';

            public bool $multiSort = false;

            public function columns(): array
            {
                return [Column::add()->field('name')->sortable()];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $ordersProperty->setAccessible(true);
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toHaveCount(1)
            ->and($orders[0])->toBe(['column' => 'name', 'direction' => 'asc']);
    });

    it('should apply multiple sorts when multiSort is enabled', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = 'name';

            public string $sortDirection = 'asc';

            public bool $multiSort = true;

            public array $sortArray = [
                'name' => 'asc',
                'price' => 'desc',
                'category_id' => 'asc',
            ];

            public function columns(): array
            {
                return [
                    Column::add()->field('name')->sortable(),
                    Column::add()->field('price')->sortable(),
                    Column::add()->field('category_id')->sortable(),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toHaveCount(3)
            ->and($orders[0])->toBe(['column' => 'name', 'direction' => 'asc'])
            ->and($orders[1])->toBe(['column' => 'price', 'direction' => 'desc'])
            ->and($orders[2])->toBe(['column' => 'category_id', 'direction' => 'asc']);
    });

    it('should not modify builder when sortField is blank', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = '';

            public string $sortDirection = '';

            public bool $multiSort = false;
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toBeEmpty();
    });

    it('should handle descending sort direction', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = 'created_at';

            public string $sortDirection = 'desc';

            public bool $multiSort = false;

            public function columns(): array
            {
                return [Column::add()->field('created_at')->sortable()];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toHaveCount(1)
            ->and($orders[0])->toBe(['column' => 'created_at', 'direction' => 'desc']);
    });

    it('should prioritize multiSort over single sort', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = 'name';

            public string $sortDirection = 'asc';

            public bool $multiSort = true;

            public array $sortArray = [
                'price' => 'desc',
                'category_id' => 'asc',
            ];

            public function columns(): array
            {
                return [
                    Column::add()->field('price')->sortable(),
                    Column::add()->field('category_id')->sortable(),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toHaveCount(2)
            ->and($orders[0])->toBe(['column' => 'price', 'direction' => 'desc'])
            ->and($orders[1])->toBe(['column' => 'category_id', 'direction' => 'asc']);
    });
});

describe('Scout Pipeline Integration', function () {
    it('should apply all pipelines in sequence', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $search = 'pizza';

            public array $filters = [
                'select' => ['category_id' => '1'],
            ];

            public string $sortField = 'name';

            public string $sortDirection = 'asc';

            public bool $multiSort = false;

            public function columns(): array
            {
                return [Column::add()->field('name')->sortable()];
            }

            public function filters(): array
            {
                return [
                    Filter::select('category_id'),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');

        $searchPipeline = new Search($component);
        $builder = $searchPipeline->handle($builder, fn ($builder) => $builder);

        $filtersPipeline = new Filters($component);
        $builder = $filtersPipeline->handle($builder, fn ($builder) => $builder);

        $sortingPipeline = new Sorting($component);
        $builder = $sortingPipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($builder);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($builder);

        expect($builder->query)->toBe('pizza,')
            ->and($builder->wheres)->toHaveCount(1)
            ->and($builder->wheres[0])->toBe(['field' => 'category_id', 'operator' => '=', 'value' => '1'])
            ->and($orders)->toHaveCount(1)
            ->and($orders[0])->toBe(['column' => 'name', 'direction' => 'asc']);
    });
});

describe('Scout Pipeline field/direction validation', function () {
    it('only applies filters for fields declared in filters()', function () {
        $component = new class() extends PowerGridComponent
        {
            public array $filters = [
                'select' => [
                    'category_id' => '1',
                    'undeclared_column' => 'x',
                ],
            ];

            public function filters(): array
            {
                return [
                    Filter::select('category_id'),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Filters($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->wheres)->toHaveCount(1)
            ->and($result->wheres[0])->toBe(['field' => 'category_id', 'operator' => '=', 'value' => '1']);
    });

    it('does not apply any filter when filters() is empty', function () {
        $component = new class() extends PowerGridComponent
        {
            public array $filters = [
                'select' => ['category_id' => '1'],
            ];
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Filters($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        expect($result->wheres)->toBeEmpty();
    });

    it('normalizes an unexpected sort direction to asc (single sort)', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = 'name';

            public string $sortDirection = 'asc, (select 1)';

            public bool $multiSort = false;

            public function columns(): array
            {
                return [Column::add()->field('name')->sortable()];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toHaveCount(1)
            ->and($orders[0])->toBe(['column' => 'name', 'direction' => 'asc']);
    });

    it('normalizes an unexpected sort direction to asc (multi sort)', function () {
        $component = new class() extends PowerGridComponent
        {
            public string $sortField = 'name';

            public string $sortDirection = 'asc';

            public bool $multiSort = true;

            public array $sortArray = [
                'name' => 'desc',
                'price' => 'asc, (select 1)',
            ];

            public function columns(): array
            {
                return [
                    Column::add()->field('name')->sortable(),
                    Column::add()->field('price')->sortable(),
                ];
            }
        };

        $builder = new ScoutBuilder(new Dish(), '');
        $pipeline = new Sorting($component);

        $result = $pipeline->handle($builder, fn ($builder) => $builder);

        $reflection = new ReflectionClass($result);
        $ordersProperty = $reflection->getProperty('orders');
        $orders = $ordersProperty->getValue($result);

        expect($orders)->toHaveCount(2)
            ->and($orders[0])->toBe(['column' => 'name', 'direction' => 'desc'])
            ->and($orders[1])->toBe(['column' => 'price', 'direction' => 'asc']);
    });
});
