<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
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

    private $collection;

    protected string $paginationTheme = 'bootstrap';

    protected $listeners = [
        'eventChangeDatePiker' => 'eventChangeDatePiker',
        'eventChangeInput'     => 'eventChangeInput',
        'eventToggleChanged'   => 'eventChangeInput',
        'eventMultiSelect'     => 'eventMultiSelect'
    ];

    private string $data_source;

    private $dataSource;

    /**
     * Apply checkbox, perPage and search view and theme
     */
    public function setUp()
    {
        $this->showPerPage();
    }

    /**
     * @return array
     */
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
        $this->columns = $this->columns();

        $this->paginationTheme = config('livewire-powergrid.theme');

        $this->renderFilter();

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }
    }

    /**
     * @throws \Exception
     */
    public function render()
    {
        $this->setUp();

        $this->columns    = $this->columns();
        $this->dataSource = $this->dataSource()->make();

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }

        if (is_array($this->dataSource)) {

            $data = collect($this->dataSource);
            if (!empty($this->search)) {
                $data = $data->filter(function ($row) {
                    foreach ($this->columns() as $column) {
                        $field = $column->field;
                        if (Str::contains(strtolower($row->{$field}), strtolower($this->search))) {
                            return false !== stristr($row->{$field}, strtolower($this->search));
                        }
                    }

                    return false;
                });
            }

            $data = Collection::filter($this->filters, $data);

            $data = $this->applySorting($data);

            if ($data->count()) {
                $this->filtered = $data->pluck('id')->toArray();
                $data           = Collection::paginate($data, ($this->perPage == '0') ? $data->count() : $this->perPage);
            }

        } else {

            $data = $this->dataSource;

            $table = $data->getModel()->getTable();

            $query = $data->where(function ($query) use ($table) {
                if ($this->search != '' && $query->getModel()->count() === 0) {
                    $query->where(function ($query) use ($table) {
                        foreach ($this->columns() as $column) {
                            $hasColumn = Schema::hasColumn($table, $column->field);
                            if ($hasColumn) {
                                $query->orWhere($column->field, 'like', '%' . $this->search . '%');
                            }
                        }
                    });
                }

                if (count($this->filters)) {
                    Model::filter($this->filters, $query);
                }
            })->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

            $updatedItems = $query->getCollection();

            $updatedItems = $updatedItems->transform(function ($row) {
                $columns = $this->addColumns()->columns;
                foreach ($columns as $key => $column) {
                    $row->{$key} = $column($row);
                }

                return $row;
            });

            $data = $query->setCollection($updatedItems);
        }

        return $this->renderView($data);
    }

    private function renderView($data)
    {
        $theme   = config('livewire-powergrid.theme');
        $version = config('livewire-powergrid.theme_versions')[$theme];

        return view('livewire-powergrid::' . $theme . '.' . $version . '.table', [
            'data' => $data
        ]);
    }

    /**
     * @throws \Exception
     */
    public function fromCollection($cached = '')
    {
        if (filled($cached)) {
            cache()->forget($this->id);

            return cache()->rememberForever($this->id, function () use ($cached) {
                return $cached;
            });
        }

        $cache = config('livewire-powergrid.cached_data');
        if ($cache) {
            return \cache()->rememberForever($this->id, function () {
                return new BaseCollection($this->dataSource());
            });
        }

        return new BaseCollection($this->dataSource());
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

            if (is_array($this->dataSource())) {
                $collection = $this->dataSource()->collection();

                $cached = $collection->map(function ($row) use ($data) {
                    $field = $data['field'];
                    if ($row->id === $data['id']) {
                        $row->{$field} = $data['value'];
                    }

                    return $row;
                });

                $this->fromCollection($cached);
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
    private function prepareToExport()
    {
        if (filled($this->checkboxValues)) {
            $inClause = $this->checkboxValues;
        } else {
            $inClause = $this->filtered;
        }

        if (is_array($this->dataSource())) {
            if ($inClause) {
                return $this->fromCollection()->whereIn($this->primaryKey, $inClause);
            }

            return $this->fromCollection();
        }

        if ($inClause) {
            return $this->dataSource()->whereIn($this->primaryKey, $inClause)->get()->transform(function ($row) {
                $columns = $this->addColumns()->columns;
                foreach ($columns as $key => $column) {
                    $row->{$key} = $column($row);
                }

                return $row;
            });
        }

        return $this->dataSource()->get()->transform(function ($row) {
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
            ->fromCollection($this->columns(), $this->prepareToExport())
            ->download();
    }

    /**
     * @throws \Exception
     */
    public function exportToCsv(): BinaryFileResponse
    {
        return (new ExportToCsv())
            ->fileName($this->exportFileName)
            ->fromCollection($this->columns(), $this->prepareToExport())
            ->download();
    }
}
