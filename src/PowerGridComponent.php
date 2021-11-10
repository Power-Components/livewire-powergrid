<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;
use Livewire\{Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Helpers\{Collection, Model};
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use PowerComponents\LivewirePowerGrid\Traits\{BatchableExport, Checkbox, Exportable, Filter, WithSorting};
use Psr\SimpleCache\InvalidArgumentException;

class PowerGridComponent extends Component
{
    use WithPagination;
    use Exportable;
    use WithSorting;
    use Checkbox;
    use Filter;
    use BatchableExport;

    public array $headers = [];

    public bool $searchInput = false;

    public string $search = '';

    public bool $perPageInput = false;

    public int $perPage = 10;

    public array $columns = [];

    public array $perPageValues = [10, 25, 50, 100, 0];

    public string $recordCount = '';

    public array $filtered = [];

    public string $primaryKey = 'id';

    public bool $isCollection = false;

    protected string $paginationTheme = 'tailwind';

    protected ThemeBase $powerGridTheme;

    public $datasource;

    protected $listeners = [
        'eventChangeDatePiker' => 'eventChangeDatePiker',
        'eventInputChanged'    => 'eventInputChanged',
        'eventToggleChanged'   => 'eventInputChanged',
        'eventMultiSelect'     => 'eventMultiSelect',
        'eventRefresh'         => '$refresh',
        'eventToggleColumn'    => 'toggleColumn',
        'editEvent',
        'deleteEvent',
    ];

    public bool $toggleColumns = false;

    public array $relationSearch = [];

    /**
     * Apply checkbox, perPage and search view and theme
     */
    public function setUp()
    {
        $this->showPerPage();
    }

    public function template(): ?string
    {
        return null;
    }

    public function columns(): array
    {
        return [];
    }

    public function datasource()
    {
        return null;
    }

    public function addColumns()
    {
        return null;
    }

    public function relationSearch(): array
    {
        return [];
    }

    /**
     * @return $this
     * Show search input into component
     */
    public function showSearchInput(): PowerGridComponent
    {
        $this->searchInput = true;

        return $this;
    }

    /**
     * default full. other: short, min
     * @param string $mode
     * @return $this
     */
    public function showRecordCount(string $mode = 'full'): PowerGridComponent
    {
        $this->recordCount = $mode;

        return $this;
    }

    /**
     * default false
     * @return $this
     */
    public function showToggleColumns(): PowerGridComponent
    {
        $this->toggleColumns = true;

        return $this;
    }

    /**
     * @param string $attribute
     * @return PowerGridComponent
     */
    public function showCheckBox(string $attribute = 'id'): PowerGridComponent
    {
        $this->checkbox          = true;
        $this->checkboxAttribute = $attribute;

        return $this;
    }

    /**
     * @param int $perPage
     * @return $this
     */
    public function showPerPage(int $perPage = 10): PowerGridComponent
    {
        if (\Str::contains($perPage, $this->perPageValues)) {
            $this->perPageInput = true;
            $this->perPage      = $perPage;
        }

        return $this;
    }

    public function mount($datasource = null)
    {
        $this->setUp();

        $this->columns = $this->columns();

        $this->paginationTheme = PowerGrid::theme($this->template() ?? powerGridTheme())::paginationTheme();

        $this->renderFilter();

        $this->datasource = $datasource;
    }

    public function render()
    {
        $this->powerGridTheme = PowerGrid::theme($this->template() ?? powerGridTheme())->apply();

        $this->columns = collect($this->columns)->map(function ($column) {
            return (object) $column;
        })->toArray();

        $this->relationSearch = $this->relationSearch();

        $data = $this->fillData();

        if (method_exists($this, 'initActions')) {
            $this->initActions();
            $this->headers = $this->header();
        }

        return $this->renderView($data);
    }

    private function renderView($data)
    {
        return view($this->powerGridTheme->layout->table, [
            'data'  => $data,
            'theme' => $this->powerGridTheme,
            'table' => 'livewire-powergrid::components.table',
        ]);
    }

    private function resolveModel($datasource = null)
    {
        if (blank($datasource)) {
            return $this->datasource();
        }

        return $datasource;
    }

    private function resolveCollection($datasource = null, $cached = '')
    {
        if (filled($cached)) {
            cache()->forget($this->id);

            return cache()->rememberForever($this->id, function () use ($cached) {
                return $cached;
            });
        }

        if (!powerGridCache()) {
            return new BaseCollection($this->datasource());
        }

        return cache()->rememberForever($this->id, function () use ($datasource) {
            if (is_array($datasource)) {
                return new BaseCollection($datasource);
            }
            if (is_a($datasource, BaseCollection::class)) {
                return $datasource;
            }

            return new BaseCollection($datasource);
        });
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function eventInputChanged(array $data): void
    {
        $update = $this->update($data);

        if (!$update) {
            session()->flash('error', $this->updateMessages('error', data_get($data, 'field')));

            return;
        }
        session()->flash('success', $this->updateMessages('success', data_get($data, 'field')));

        if (!is_array($this->datasource)) {
            return;
        }

        $this->fillData();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        return false;
    }

    /**
     * @param string $status
     * @param string $field
     * @return string
     */
    public function updateMessages(string $status, string $field = '_default_message'): string
    {
        $updateMessages = [
            'success' => [
                '_default_message' => __('Data has been updated successfully!'),
                'status'           => __('Custom Field updated successfully!'),
            ],
            'error' => [
                '_default_message' => __('Error updating the data.'),
                //'custom_field' => __('Error updating custom field.'),
            ],
        ];

        return ($updateMessages[$status][$field] ?? $updateMessages[$status]['_default_message']);
    }

    public function checkedValues(): array
    {
        return $this->checkboxValues;
    }

    /**
     * @return LengthAwarePaginator|BaseCollection|mixed
     * @throws InvalidArgumentException
     */
    public function fillData()
    {
        /** @var Builder | array | BaseCollection $datasource */

        if (cache()->has($this->id)) {
            $datasource = collect(cache()->get($this->id))->toArray();
        } else {
            $datasource = $this->datasource() ?: $this->datasource;
        }

        $this->instanceOfCollection($datasource);

        if (filled($this->search)) {
            $this->gotoPage(1);
        }

        if ($this->isCollection) {
            $filters = Collection::query($this->resolveCollection($datasource))
                ->setColumns($this->columns)
                ->setSearch($this->search)
                ->setFilters($this->filters)
                ->filterContains()
                ->filter();

            $results = $this->applySorting($filters);

            if ($results->count()) {
                $this->filtered = $results->pluck('id')->toArray();

                $paginated = Collection::paginate($results, $this->perPage);
                $results   = $paginated->setCollection($this->transform($paginated->getCollection()));
            }

            return $results;
        }

        $results = $this->resolveModel($datasource)
            ->where(function (Builder $query) {
                Model::query($query)
                    ->setColumns($this->columns)
                    ->setSearch($this->search)
                    ->setRelationSearch($this->relationSearch)
                    ->setFilters($this->filters)
                    ->filterContains()
                    ->filter();
            })->orderByRaw("$this->sortField+0 $this->sortDirection")
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->perPage > 0) {
            $results = $results->paginate($this->perPage);
        } else {
            $results = $results->paginate($results->count());
        }

        $this->total = $results->total();

        return $results->setCollection($this->transform($results->getCollection()));
    }

    private function instanceOfCollection($datasource): void
    {
        $checkDatasource = (
            is_a($datasource, PowerGrid::class)
            || is_array($datasource)
            || is_a($datasource, BaseCollection::class)
        );
        if ($checkDatasource) {
            $this->isCollection = true;
        }
    }

    private function transform($results)
    {
        if (is_a($this->addColumns(), PowerGridCollection::class)
            || is_a($this->addColumns(), PowerGridEloquent::class)
        ) {
            return $results->transform(function ($row) {
                $row = (object) $row;
                $columns = $this->addColumns()->columns;
                foreach ($columns as $key => $column) {
                    $row->{$key} = $column($row);
                }

                return $row;
            });
        }

        return $results;
    }

    public function updatedPage()
    {
        $this->checkboxAll = false;
    }

    public function toggleColumn($field): void
    {
        $this->columns = collect($this->columns)->map(function ($column) use ($field) {
            if (data_get($column, 'field') === $field) {
                $column['hidden'] = !data_get($column, 'hidden');
            }

            return (object) $column;
        })->toArray();
    }
}
