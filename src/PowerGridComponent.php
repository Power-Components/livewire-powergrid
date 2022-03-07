<?php

namespace PowerComponents\LivewirePowerGrid;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Pagination\{AbstractPaginator};
use Illuminate\Support\{Collection as BaseCollection, Facades\Crypt, Str};
use Livewire\{Component, WithPagination};
use PowerComponents\LivewirePowerGrid\Helpers\{Collection, Helpers, Model, SqlSupport};
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use PowerComponents\LivewirePowerGrid\Traits\{BatchableExport, Checkbox, Exportable, Filter, WithSorting};
use stdClass;

class PowerGridComponent extends Component
{
    use WithPagination;
    use Exportable;
    use WithSorting;
    use Checkbox;
    use HasAttributes;
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

    public string $currentTable = '';

    /** @var \Illuminate\Database\Eloquent\Collection|array|Builder $datasource */
    public $datasource;

    /** @var \Illuminate\Support\Collection $withoutPaginatedData */
    public $withoutPaginatedData;

    public bool $toggleColumns = false;

    public array $relationSearch = [];

    public bool $ignoreTablePrefix = true;

    public bool $showUpdateMessages = false;

    public string $tableName = 'default';

    public bool $headerTotalColumn = false;

    public bool $footerTotalColumn = false;

    protected string $paginationTheme = 'tailwind';

    protected ThemeBase $powerGridTheme;

    public array $persist = [];

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

    public function showCheckBox(string $attribute = 'id'): PowerGridComponent
    {
        $this->checkbox          = true;
        $this->checkboxAttribute = $attribute;

        return $this;
    }

    /**
     * filters, columns
     * @return $this
     */
    public function persist(array $tableItems): PowerGridComponent
    {
        $this->persist = $tableItems;

        return $this;
    }

    public function mount(): void
    {
        $this->setUp();

        $this->columns = $this->columns();

        $this->resolveTotalRow();

        $this->renderFilter();

        $this->restoreState();
    }

    /**
     * Apply checkbox, perPage and search view and theme
     * @return void
     */
    public function setUp()
    {
        $this->showPerPage();
    }

    /**
     * @return $this
     */
    public function showPerPage(int $perPage = 10): PowerGridComponent
    {
        if (Str::contains((string) $perPage, $this->perPageValues)) {
            $this->perPageInput = true;
            $this->perPage      = $perPage;
        }

        return $this;
    }

    public function columns(): array
    {
        return [];
    }

    private function resolveTotalRow(): void
    {
        collect($this->columns())->each(function (Column $column) {
            if ($column->sum['header'] || $column->count['header'] || $column->min['header'] || $column->avg['header'] || $column->max['header']) {
                $this->headerTotalColumn = true;
            }
            if ($column->sum['footer'] || $column->count['footer'] || $column->min['footer'] || $column->avg['footer'] || $column->max['footer']) {
                $this->footerTotalColumn = true;
            }
        });
    }

    /**
     * @return Application|Factory|View
     * @throws Exception
     */
    public function render()
    {
        /** @var ThemeBase $themeBase */
        $themeBase = PowerGrid::theme($this->template() ?? powerGridTheme());

        $this->powerGridTheme = $themeBase->apply();

        $this->columns = collect($this->columns)->map(function ($column) {
            return (object) $column;
        })->toArray();

        $this->relationSearch = $this->relationSearch();

        $data = $this->fillData();

        if (method_exists($this, 'initActions')) {
            $this->initActions();
            if (method_exists($this, 'header')) {
                $this->headers = $this->header();
            }
        }

        return $this->renderView($data);
    }

    public function template(): ?string
    {
        return null;
    }

    public function relationSearch(): array
    {
        return [];
    }

    /**
     * @return AbstractPaginator|BaseCollection
     * @throws Exception
     */
    public function fillData()
    {
        /** @var Builder|BaseCollection|\Illuminate\Database\Eloquent\Collection $datasource */
        $datasource = (!empty($this->datasource)) ? $this->datasource : $this->datasource();

        $this->isCollection = is_a((object) $datasource, BaseCollection::class);

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

            if ($this->headerTotalColumn || $this->footerTotalColumn) {
                $this->withoutPaginatedData = $results->values()
                    ->map(fn ($item) => (array) $item);
            }

            if ($results->count()) {
                $this->filtered = $results->pluck('id')->toArray();

                $paginated = Collection::paginate($results, $this->perPage);
                $results   = $paginated->setCollection($this->transform($paginated->getCollection()));
            }

            return $results;
        }

        /** @phpstan-ignore-next-line */
        $this->currentTable = $datasource->getModel()->getTable();

        if (Str::of($this->sortField)->contains('.') || $this->ignoreTablePrefix) {
            $sortField = $this->sortField;
        } else {
            $sortField = $this->currentTable . '.' . $this->sortField;
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
            $sortFieldType = SqlSupport::getSortFieldType($sortField);

            if (SqlSupport::isValidSortFieldType($sortFieldType)) {
                $results->orderByRaw(SqlSupport::sortStringAsNumber($sortField) . ' ' . $this->sortDirection);
            }
        }

        $results = $results->orderBy($sortField, $this->sortDirection);

        if ($this->headerTotalColumn || $this->footerTotalColumn) {
            $this->withoutPaginatedData = $this->transform($results->get());
        }

        if ($this->perPage > 0) {
            $results = $results->paginate($this->perPage);
        } else {
            $results = $results->paginate($results->count());
        }

        $this->total = $results->total();

        return $results->setCollection($this->transform($results->getCollection()));
    }

    /**
     * @return null
     */
    public function datasource()
    {
        return null;
    }

    /**
     * @param array|BaseCollection|Builder|null $datasource
     * @throws Exception
     */
    private function resolveCollection($datasource = null): BaseCollection
    {
        if (!boolval(config('livewire-powergrid.cached_data', false))) {
            return new BaseCollection($this->datasource());
        }

        return cache()->rememberForever($this->id, function () use ($datasource) {
            if (is_array($datasource)) {
                return new BaseCollection($datasource);
            }
            if (is_a((object) $datasource, BaseCollection::class)) {
                return $datasource;
            }

            return new BaseCollection($datasource);
        });
    }

    private function transform(BaseCollection $results): BaseCollection
    {
        if (
            !is_a((object) $this->addColumns(), PowerGridEloquent::class)
        ) {
            return $results;
        }

        return $results->map(function ($row) {
            /** @var stdClass $columns */
            $columns = $this->addColumns();

            $columns = collect($columns->columns);

            /** @phpstan-ignore-next-line */
            $data = $columns->mapWithKeys(fn ($column, $columnName) => (object) [$columnName => $column((object) $row)]);

            if (count($this->actionRules())) {
                $rules = resolve(Helpers::class)->resolveRules($this->actionRules(), (object) $row);
            }

            $mergedData = $data->merge($rules ?? []);

            return $row instanceof \Illuminate\Database\Eloquent\Model
                ? tap($row)->forceFill($mergedData->toArray())
                : (object) $mergedData->toArray();
        });
    }

    /**
     * @return null
     */
    public function addColumns()
    {
        return null;
    }

    public function actionRules(): array
    {
        return [];
    }

    /**
     * @param array|BaseCollection|Builder|null $datasource
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
     * @param AbstractPaginator|BaseCollection $data
     * @return Application|Factory|View
     */
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
     * @throws Exception
     */
    public function inputTextChanged(array $data): void
    {
        $update = $this->update($data);

        $this->fillData();

        if (!$this->showUpdateMessages) {
            return;
        }

        if (!$update) {
            session()->flash('error', $this->updateMessages('error', $data['field']));

            return;
        }
        session()->flash('success', $this->updateMessages('success', $data['field']));
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

    public function updatedPage(): void
    {
        $this->checkboxAll = false;
    }

    /**
     * @throws Exception
     */
    public function toggleColumn(string $field): void
    {
        $this->columns = collect($this->columns)->map(function ($column) use ($field) {
            if (data_get($column, 'field') === $field) {
                data_set($column, 'hidden', !data_get($column, 'hidden'));
            }

            return (object) $column;
        })->toArray();

        $this->persistState('columns');

        $this->fillData();
    }

    private function persistState(string $tableItem):void
    {
        $state = [];
        if (in_array('columns', $this->persist)) {
            $state['columns'] = collect($this->columns)
                ->map(fn ($column)         => (object) $column)
                ->mapWithKeys(fn ($column) => [$column->field => $column->hidden])
                ->toArray();
        }
        if (in_array('filters', $this->persist)) {
            $state['filters']        = $this->filters;
            $state['enabledFilters'] = $this->enabledFilters;
        }

        if (!empty($this->persist)) {
            $url  = parse_url(strval(filter_input(INPUT_SERVER, 'HTTP_REFERER')));
            $path = $url && array_key_exists('path', $url) ? $url['path'] : '/';
            setcookie('pg:' . $this->tableName, strval(json_encode($state)), now()->addYear()->unix(), $path);
        }
    }

    private function restoreState():void
    {
        if (empty($this->persist)) {
            return;
        }

        $cookie = filter_input(INPUT_COOKIE, 'pg:' . $this->tableName);
        if (is_null($cookie)) {
            return;
        }

        $state = (array) json_decode(strval($cookie), true);

        if (in_array('columns', $this->persist) && array_key_exists('columns', $state)) {
            $this->columns = collect($this->columns)->map(function ($column) use ($state) {
                if (!$column->forceHidden && array_key_exists($column->field, $state['columns'])) {
                    data_set($column, 'hidden', $state['columns'][$column->field]);
                }

                return (object) $column;
            })->toArray();
        }

        if (in_array('filters', $this->persist) && array_key_exists('filters', $state)) {
            $this->filters        = $state['filters'];
            $this->enabledFilters = $state['enabledFilters'];
        }
    }

    /**
     * @param string $fileName
     * @param array|string[] $type
     * @param array $options
     * @return PowerGridComponent
     */
    public function showExportOption(string $fileName, array $type = ['excel', 'csv'], array $options = ['deleteAfterDownload' => true]): PowerGridComponent
    {
        $this->exportActive   = true;
        $this->exportFileName = $fileName;
        $this->exportType     = $type;
        $this->exportOptions  = $options;

        return $this;
    }

    /**
     * @return array
     */
    protected function getListeners()
    {
        return [
            'pg:datePicker-' . $this->tableName   => 'datePikerChanged',
            'pg:editable-' . $this->tableName     => 'inputTextChanged',
            'pg:toggleable-' . $this->tableName   => 'inputTextChanged',
            'pg:multiSelect-' . $this->tableName  => 'multiSelectChanged',
            'pg:toggleColumn-' . $this->tableName => 'toggleColumn',
            'pg:eventRefresh-' . $this->tableName => '$refresh',
        ];
    }
}
