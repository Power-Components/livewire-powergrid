<?php

namespace PowerComponents\LivewirePowerGrid;

use Livewire\Component;
use Livewire\WithPagination;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use PowerComponents\LivewirePowerGrid\Traits\Checkbox;
use PowerComponents\LivewirePowerGrid\Traits\Filter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PowerGridComponent extends Component
{
    use WithPagination,
        Checkbox,
        Filter;

    public array $headers = [];

    public bool $search_input = false;

    public string $search = '';

    public bool $perPage_input = false;

    public string $orderBy = 'id';

    public bool $orderAsc = false;

    public int $perPage = 10;

    public array $columns = [];

    public array $perPageValues = [10, 25, 50, 100, 0];

    public string $sortIcon = '&#8597;';

    public string $sortAscIcon = '&#8593;';

    public string $sortDescIcon = '&#8595;';

    public string $record_count = '';

    public bool $export_option = false;

    public string $export_file_name = 'download';

    public array $export_type = [];

    public array $filtered = [];

    public $transform;

    public string $primaryKey = 'id';

    public string $download_status;

    private $collection;

    protected string $paginationTheme = 'bootstrap';

    protected $listeners = [
        'eventChangeDatePiker' => 'eventChangeDatePiker',
        'eventChangeInput'     => 'eventChangeInput',
        'eventToggleChanged'   => 'eventChangeInput',
        'eventMultiSelect'     => 'eventMultiSelect'
    ];

    private string $data_source;

    private DataSource $dataSource;

    /**
     * PowerGridComponent constructor.
     */
    public function __construct()
    {
        $this->dataSource = resolve(DataSource::class);

        return parent::__construct();
    }

    /**
     * Apply checkbox, perPage and search view and theme
     */
    public function setUp()
    {
        $this->showPerPage()
            ->showExportOption('download', ['excel', 'csv']);
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

    public function transform()
    {
        return null;
    }

    /**
     * @return $this
     * Show search input into component
     */
    public function showSearchInput(): PowerGridComponent
    {
        $this->search_input = true;

        return $this;
    }

    /**
     * @return $this
     * Show show export button
     */
    public function showExportOption($fileName, $type = ['excel', 'csv']): PowerGridComponent
    {
        $this->export_option    = true;
        $this->export_file_name = $fileName;
        $this->export_type      = $type;

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
        $this->checkbox           = true;
        $this->checkbox_attribute = $attribute;

        return $this;
    }

    /**
     * @param int $perPage
     * @return $this
     */
    public function showPerPage(int $perPage = 10): PowerGridComponent
    {
        if (\Str::contains($perPage, $this->perPageValues)) {
            $this->perPage_input = true;
            $this->perPage       = $perPage;
        }

        return $this;
    }

    public function mount()
    {
        $this->columns = $this->columns();

        $this->paginationTheme = config('livewire-powergrid.theme');

        $this->renderFilter();

        $this->setUp();

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }
    }

    /**
     * @throws \Exception
     */
    public function render()
    {
        $this->columns = $this->columns();

        $this->dataSource->id($this->id)
            ->perPage($this->perPage)
            ->dataSource($this->dataSource())
            ->columns($this->columns())
            ->transform($this->transform())
            ->filters($this->filters);

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }

        if (is_array($this->dataSource())) {
            $data_source = $this->dataSource->collection();

            $data = Helpers\Collection::search($data_source, $this->search, $this->columns());
            $data = Helpers\Collection::filter($data, $this->filters)
                ->sortBy($this->orderBy, SORT_REGULAR, $this->orderAsc);

            if ($data->count()) {
                $this->filtered = $data->pluck('id')->toArray();
                $data           = Helpers\Collection::paginate($data, ($this->perPage == '0') ? $data->count() : $this->perPage);
            }
        } else {
            $data = $this->dataSource->model();
        }

        return $this->renderView($data);
    }

    /**
     * @param $field
     */
    public function setOrder($field)
    {
        if ($this->orderBy === $field) {
            $this->orderAsc = !$this->orderAsc;
        }
        $this->orderBy = $field;
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
                $collection = $this->dataSource->collection();

                $cached = $collection->map(function ($row) use ($data) {
                    $field = $data['field'];
                    if ($row->id === $data['id']) {
                        $row->{$field} = $data['value'];
                    }

                    return $row;
                });

                $this->dataSource->collection($cached);
            } else {
                $this->dataSource->model();
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
        return $this->checkbox_values;
    }

    /**
     * @throws \Exception
     */
    private function prepareToExport()
    {
        if (filled($this->checkbox_values)) {
            $in_clause = $this->checkbox_values;
        } else {
            $in_clause = $this->filtered;
        }

        if (is_array($this->dataSource())) {
            if ($in_clause) {
                return $this->dataSource->collection()->whereIn($this->primaryKey, $in_clause);
            }

            return $this->dataSource->collection();
        }

        if ($in_clause) {
            return $this->dataSource()->whereIn($this->primaryKey, $in_clause)->get()->transform(function ($row) {
                $columns = $this->transform()->columns;
                foreach ($columns as $key => $column) {
                    $row->{$key} = $column($row);
                }

                return $row;
            });
        }

        return $this->dataSource()->get()->transform(function ($row) {
            $columns = $this->transform()->columns;
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
            ->fileName($this->export_file_name)
            ->fromCollection($this->columns(), $this->prepareToExport())
            ->download();
    }

    /**
     * @throws \Exception
     */
    public function exportToCsv(): BinaryFileResponse
    {
        return (new ExportToCsv())
            ->fileName($this->export_file_name)
            ->fromCollection($this->columns(), $this->prepareToExport())
            ->download();
    }
}
