<?php

namespace PowerComponents\LivewirePowerGrid;

use App\Models\Dish;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;
use Livewire\Component;
use Livewire\WithPagination;
use PowerComponents\LivewirePowerGrid\Helpers\Collection;
use PowerComponents\LivewirePowerGrid\Helpers\Model;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use PowerComponents\LivewirePowerGrid\Traits\Checkbox;
use PowerComponents\LivewirePowerGrid\Traits\Filter;
use PowerComponents\LivewirePowerGrid\Traits\WithSorting;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PowerGridComponent extends Component
{
    use WithPagination,
        WithSorting,
        Checkbox,
        Filter;

    public array $headers = [];

    public bool $searchInput = false;

    public string $search = '';

    public bool $perPageInput = false;

    public int $perPage = 10;

    public array $columns = [];

    public array $perPageValues = [10, 25, 50, 100, 0];

    public string $record_count = '';

    public bool $exportOption = false;

    public string $exportFileName = 'download';

    public array $exportType = [];

    public array $filtered = [];

    public string $primaryKey = 'id';

    public bool $isCollection = false;

    protected string $paginationTheme = 'bootstrap';

    protected $dataSource;

    protected $listeners = [
        'eventChangeDatePiker' => 'eventChangeDatePiker',
        'eventChangeInput'     => 'eventChangeInput',
        'eventToggleChanged'   => 'eventChangeInput',
        'eventMultiSelect'     => 'eventMultiSelect'
    ];

    /**
     * Apply checkbox, perPage and search view and theme
     */
    public function setUp()
    {
        $this->showPerPage();
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
        $this->record_count = $mode;

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

        $this->paginationTheme = config('livewire-powergrid.theme');

        $this->renderFilter();

    }

    /**
     * @throws \Exception
     */
    public function render()
    {
        $data = null;

        $this->columns = $this->columns();

        if (!cache()->get($this->id)) {
            $dataSource = $this->dataSource();
        } else {
            $dataSource = collect(cache()->get($this->id))->toArray();
        }

        if (is_a($dataSource, PowerGrid::class) || is_array($dataSource) || is_a($dataSource, BaseCollection::class)) {
            $this->isCollection = true;

            $collection = $this->resolveCollection($dataSource);

            $collection = Collection::filterContains($collection, $this->columns(), $this->search);

            $collection = Collection::filter($this->filters, $collection);

            $collection = $this->applySorting($collection);

            if ($collection->count()) {
                $this->filtered = $collection->pluck('id')->toArray();
                $data           = Collection::paginate($collection, ($this->perPage == '0') ? $collection->count() : $this->perPage);

                if (is_a($this->addColumns(), PowerGrid::class)) {
                    $data       = $data->setCollection(
                        $data->getCollection()->transform(function ($row) {
                            $columns = $this->addColumns()->columns;
                            foreach ($columns as $key => $column) {
                                $row->{$key} = $column($row);
                            }

                            return $row;
                        })
                    );
                }
            }
        } else {
            $model = $this->resolveModel($dataSource);

            $table = $model->getModel()->getTable();

            $model = $model->where(function ($query) use ($table) {
                Model::filterContains($query, $this->columns(), $this->search, $table);
                Model::filter($this->filters, $query);
            })->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);

            $data = $model->setCollection(
                $model->getCollection()->transform(function ($row) {
                    $columns = $this->addColumns()->columns;
                    foreach ($columns as $key => $column) {
                        $row->{$key} = $column($row);
                    }

                    return $row;
                })
            );
        }

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }

        return $this->renderView($data);
    }

    private function renderView($data)
    {
        return view('livewire-powergrid::' . powerGridTheme() . '.' . powerGridThemeVersion() . '.table', [
            'data' => $data
        ]);
    }

    public function resolveModel($dataSource = null)
    {
        if (blank($dataSource)) {
            return $this->dataSource();
        }

        return $dataSource;
    }

    /**
     * @throws \Exception
     */
    public function resolveCollection($dataSource = null, $cached = '')
    {
        if (filled($cached)) {
            cache()->forget($this->id);

            return cache()->rememberForever($this->id, function () use ($cached) {
                return $cached;
            });
        }

        if (powerGridCache()) {
            return \cache()->rememberForever($this->id, function () use ($dataSource) {
                if ($dataSource === null) {
                    return new BaseCollection($this->dataSource()->make());
                }
                if (is_array($dataSource)) {
                    return new BaseCollection($dataSource);
                }
                if (is_a($dataSource, BaseCollection::class)) {
                    return $dataSource;
                }

                return new BaseCollection($dataSource->make());
            });
        }

        return new BaseCollection($this->dataSource()->make());
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function eventChangeInput(array $data): void
    {
        $update = $this->update($data);

        if (!$update) {
            session()->flash('error', $this->updateMessages('error', $data['field']));
        } else {
            session()->flash('success', $this->updateMessages('success', $data['field']));

            if (is_array($this->dataSource)) {
                $collection = $this->dataSource;

                $cached = $collection->map(function ($row) use ($data) {
                    $field = $data['field'];
                    if ($row->id === $data['id']) {
                        $row->{$field} = $data['value'];
                    }

                    return $row;
                });

                $this->resolveCollection(null, $cached);
            }
        }
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
        if (filled($this->checkboxValues)) {
            $inClause = $this->checkboxValues;
        } else {
            $inClause = $this->filtered;
        }

        if ($this->isCollection) {
            if ($inClause) {
                $data = $this->resolveCollection()->whereIn($this->primaryKey, $inClause);

                if (is_a($this->addColumns(), PowerGrid::class)) {
                    return $data->map(function ($row) {
                        foreach ($this->addColumns()->columns as $key => $column) {
                            $row->{$key} = $column($row);
                        }
                    });
                } else {
                    return $data;
                }
            }

            $data = $this->resolveCollection();

            return $data->map(function ($row) {
                foreach ($this->addColumns()->columns as $key => $column) {
                    $row->{$key} = $column($row);
                }
            });
        }

        if ($inClause) {
            return $this->resolveModel()->whereIn($this->primaryKey, $inClause)->get()
                ->transform(function ($row) {
                    $columns = $this->addColumns()->columns;
                    foreach ($columns as $key => $column) {
                        $row->{$key} = $column($row);
                    }

                    return $row;
                });
        }

        return $this->resolveModel()->get()->transform(function ($row) {
            $columns = $this->addColumns()->columns;
            foreach ($columns as $key => $column) {
                $row->{$key} = $column($row);
            }

            return $row;
        });
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
}
