<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;
use Livewire\{Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Helpers\{Collection, Model};
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use PowerComponents\LivewirePowerGrid\Traits\{BatchableExport, Checkbox, Exportable, Filter, WithSorting};

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

    /** @var \Illuminate\Database\Eloquent\Collection|array|Builder */
    public $datasource;

    /**
     * @var string[] $listeners
     */
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
     * @return void
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

    /**
     * @return null
     */
    public function datasource()
    {
        return null;
    }

    /**
     * @return null
     */
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
        if (\Str::contains((string) $perPage, $this->perPageValues)) {
            $this->perPageInput = true;
            $this->perPage      = $perPage;
        }

        return $this;
    }

    public function mount(): void
    {
        $this->setUp();

        $this->columns = $this->columns();

        $this->paginationTheme = PowerGrid::theme($this->template() ?? powerGridTheme())::paginationTheme();

        $this->renderFilter();
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

    /** @phpstan-ignore-next-line */
    private function renderView($data)
    {
        /** @phpstan-ignore-next-line */
        return view($this->powerGridTheme->layout->table, [
            'data'  => $data,
            'theme' => $this->powerGridTheme,
            'table' => 'livewire-powergrid::components.table',
        ]);
    }

    /**
     * @param null $datasource
     * @return mixed|null
     */
    private function resolveModel($datasource = null)
    {
        if (blank($datasource)) {
            return $this->datasource();
        }

        return $datasource;
    }

    /**
     * @param array|BaseCollection|Builder|null $datasource
     * @return BaseCollection
     * @throws Exception
     */
    private function resolveCollection($datasource = null)
    {
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
     * @return array|null|string
     */
    public function updateMessages(string $status, string $field = '_default_message')
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
     * @return LengthAwarePaginator
     */
    public function fillData()
    {
        /** @var Builder | array | BaseCollection $datasource */

        if (cache()->has($this->id)) {
            $datasource = collect(cache()->get($this->id))->toArray();
        } else {
            $datasource = $this->datasource() ?: $this->datasource;
        }

        $this->isCollection = is_a($datasource, BaseCollection::class);

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

        /** @var Builder $results */
        $results = $this->resolveModel($datasource)
            ->where(function (Builder $query) {
                Model::query($query)
                    ->setColumns($this->columns)
                    ->setSearch($this->search)
                    ->setRelationSearch($this->relationSearch)
                    ->setFilters($this->filters)
                    ->filterContains()
                    ->filter();
            });

        if ($this->withSortStringNumber) {
            $results->orderByRaw("$this->sortField+0 $this->sortDirection");
        }

        $results = $results->orderBy($this->sortField, $this->sortDirection);

        if ($this->perPage > 0) {
            $results = $results->paginate($this->perPage);
        } else {
            $results = $results->paginate($results->count());
        }

        $this->total = $results->total();

        return $results->setCollection($this->transform($results->getCollection()));
    }

    private function transform($results): \Illuminate\Database\Eloquent\Collection
    {
        if (is_a((object) $this->addColumns(), PowerGridCollection::class)
            || is_a((object) $this->addColumns(), PowerGridEloquent::class)
        ) {
            return $results->transform(function ($row) {
                $row = (object) $row;
                if (!is_null($this->addColumns())) {
                    $columns = $this->addColumns()->columns;
                    foreach ($columns as $key => $column) {
                        $row->{$key} = $column($row);
                    }
                }

                return $row;
            });
        }

        return $results;
    }

    public function updatedPage(): void
    {
        $this->checkboxAll = false;
    }

    public function toggleColumn($field): void
    {
        $this->columns = collect($this->columns)->map(function ($column) use ($field) {
            if (data_get($column, 'field') === $field) {
                data_set($column, 'hidden', !data_get($column, 'hidden'));
            }

            return (object) $column;
        })->toArray();

        $this->fillData();
    }

    /**
     * @param string $fileName
     * @param array|string[] $type
     * @return PowerGridComponent
     */
    public function showExportOption(string $fileName, array $type = ['excel', 'csv']): PowerGridComponent
    {
        $this->exportOption   = true;
        $this->exportFileName = $fileName;
        $this->exportType     = $type;

        return $this;
    }
}
