<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Collection as BaseCollection;
use Livewire\Component;
use Livewire\WithPagination;
use PowerComponents\LivewirePowerGrid\Helpers\Collection;
use PowerComponents\LivewirePowerGrid\Helpers\Model;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use PowerComponents\LivewirePowerGrid\Themes\ThemeBase;
use PowerComponents\LivewirePowerGrid\Traits\Checkbox;
use PowerComponents\LivewirePowerGrid\Traits\Filter;
use PowerComponents\LivewirePowerGrid\Traits\WithSorting;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PowerGridComponent extends Component
{
    use WithPagination;
    use WithSorting;
    use Checkbox;
    use Filter;

    public array $headers = [];

    public bool $searchInput = false;

    public string $search = '';

    public bool $perPageInput = false;

    public int $perPage = 10;

    public array $columns = [];

    public array $perPageValues = [10, 25, 50, 100, 0];

    public string $recordCount = '';

    public bool $exportOption = false;

    public string $exportFileName = 'download';

    public array $exportType = [];

    public array $filtered = [];

    public string $primaryKey = 'id';

    public bool $isCollection = false;

    protected string $paginationTheme = 'tailwind';

    protected ThemeBase $powerGridTheme;

    protected $dataSource;

    protected $listeners = [
        'eventChangeDatePiker' => 'eventChangeDatePiker',
        'eventInputChanged'    => 'eventInputChanged',
        'eventToggleChanged'   => 'eventInputChanged',
        'eventMultiSelect'     => 'eventMultiSelect',
        'eventRefresh'         => '$refresh',
    ];

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

    protected function dataSource()
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
     * @return $this
     * Show show export button
     */
    public function showExportOption($fileName, $type = ['excel', 'csv']): PowerGridComponent
    {
        $this->exportOption   = true;
        $this->exportFileName = $fileName;
        $this->exportType     = $type;

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

    public function mount()
    {
        $this->setUp();

        $this->columns = $this->columns();

        $this->paginationTheme = PowerGrid::theme($this->template() ?? powerGridTheme())::paginationTheme();

        $this->renderFilter();
    }

    public function render()
    {
        $this->powerGridTheme = PowerGrid::theme(powerGridTheme())->apply();

        $this->columns = $this->columns();

        $data = $this->loadData();

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
            'table' => 'livewire-powergrid::components.table'
        ]);
    }

    private function resolveModel($dataSource = null)
    {
        if (blank($dataSource)) {
            return $this->dataSource();
        }

        return $dataSource;
    }

    private function resolveCollection($dataSource = null, $cached = '')
    {
        if (filled($cached)) {
            cache()->forget($this->id);

            return cache()->rememberForever($this->id, function () use ($cached) {
                return $cached;
            });
        }

        if (!powerGridCache()) {
            return new BaseCollection($this->dataSource()->make());
        }

        return \cache()->rememberForever($this->id, function () use ($dataSource) {
            if (is_array($dataSource)) {
                return new BaseCollection($dataSource);
            }
            if (is_a($dataSource, BaseCollection::class)) {
                return $dataSource;
            }

            return new BaseCollection($dataSource->make());
        });
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function eventInputChanged(array $data): void
    {
        $update = $this->update($data);

        if (!$update) {
            session()->flash('error', $this->updateMessages('error', $data['field']));

            return;
        }
        session()->flash('success', $this->updateMessages('success', $data['field']));

        if (!is_array($this->dataSource)) {
            return;
        }

        $this->loadData();
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
            "error" => [
                '_default_message' => __('Error updating the data.'),
                //'custom_field' => __('Error updating custom field.'),
            ]
        ];

        return ($updateMessages[$status][$field] ?? $updateMessages[$status]['_default_message']);
    }

    public function checkedValues(): array
    {
        return $this->checkboxValues;
    }

    /**
     * @throws \Exception
     */
    public function prepareToExport()
    {
        $inClause = $this->filtered;

        if (filled($this->checkboxValues)) {
            $inClause = $this->checkboxValues;
        }

        if ($this->isCollection) {
            if ($inClause) {
                $results = $this->resolveCollection()->whereIn($this->primaryKey, $inClause);

                return $this->transform($results);
            }

            return $this->transform($this->resolveCollection());
        }

        if ($inClause) {
            $results = $this->resolveModel()->whereIn($this->primaryKey, $inClause)->get();

            return $this->transform($results);
        }

        $results = $this->resolveModel()->get();

        return $this->transform($results);
    }

    /**
     * @throws \Exception
     */
    public function exportToXLS(): BinaryFileResponse
    {
        return (new ExportToXLS())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport())
            ->download();
    }

    /**
     * @throws \Exception
     */
    public function exportToCsv(): BinaryFileResponse
    {
        return (new ExportToCsv())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport())
            ->download();
    }

    private function loadData()
    {
        if (cache()->has($this->id)) {
            $dataSource = collect(cache()->get($this->id))->toArray();
        } else {
            $dataSource = $this->dataSource();
        }

        $isCollection = $this->instanceOfCollection($dataSource);

        if ($isCollection) {
            $this->isCollection = true;

            $filters = Collection::query($this->resolveCollection($dataSource))
                ->setColumns($this->columns())
                ->setSearch($this->search)
                ->setFilters($this->filters)
                ->filterContains()
                ->filter();

            $results = $this->applySorting($filters);

            if ($results->count()) {
                $this->filtered = $results->pluck('id')->toArray();

                $paginated = Collection::paginate($results, $this->perPage);

                if (is_a($this->addColumns(), PowerGridCollection::class)) {
                    $results = $paginated->setCollection($this->transform($paginated->getCollection()));
                }
            }
        } else {
            $model = $this->resolveModel($dataSource);

            $results = $model->where(function ($query) {
                Model::query($query)
                    ->setColumns($this->columns())
                    ->setSearch($this->search)
                    ->setRelationSearch($this->relationSearch())
                    ->setFilters($this->filters)
                    ->filterContains()
                    ->filter();
            })->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);

            $results = $results->setCollection($this->transform($results->getCollection()));
        }

        return $results;
    }

    private function instanceOfCollection($dataSource): bool
    {
        return (is_a($dataSource, PowerGrid::class) || is_array($dataSource) || is_a($dataSource, BaseCollection::class));
    }

    private function transform($results)
    {
        if (is_a($this->addColumns(), PowerGridCollection::class) || is_a($this->addColumns(), PowerGridEloquent::class)) {

            return $results->transform(function ($row) {
                $row = (object)$row;
                $columns = $this->addColumns()->columns;
                foreach ($columns as $key => $column) {
                    $row->{$key} = $column($row);
                }

                return $row;
            });
        }

        return $results;
    }
}
