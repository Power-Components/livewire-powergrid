<?php

namespace PowerComponents\LivewirePowerGrid;

use Livewire\Component;
use Livewire\WithPagination;
use PowerComponents\LivewirePowerGrid\Helpers\Collection;
use PowerComponents\LivewirePowerGrid\Traits\Checkbox;
use PowerComponents\LivewirePowerGrid\Traits\ExportExcel;
use PowerComponents\LivewirePowerGrid\Traits\Filter;

class PowerGridComponent extends Component
{

    use WithPagination,
        Checkbox,
        ExportExcel,
        Filter;

    /**
     * @var array
     */
    public array $headers = [];
    /**
     * @var bool
     */
    public bool $search_input = false;
    /**
     * @var string
     */
    public string $search = '';
    /**
     * @var bool
     */
    public bool $perPage_input = false;
    /**
     * @var string
     */
    public string $orderBy = 'id';
    /**
     * @var bool
     */
    public bool $orderAsc = false;
    /**
     * @var
     */
    public $perPage = 10;
    /**
     * @var array
     */
    private array $searchable = [];
    /**
     * @var array
     */
    public array $columns = [];
    /**
     * @var string
     */
    protected string $paginationTheme = 'bootstrap';
    /**
     * @var array
     */
    public array $perPageValues = [10, 25, 50, 100, 0];
    /**
     * @var string
     */
    public string $sortIcon = '&#8597;';
    /**
     * @var string
     */
    public string $sortAscIcon = '&#8593;';
    /**
     * @var string
     */
    public string $sortDescIcon = '&#8595;';
    /**
     * @var string
     */
    public string $record_count = '';

    /**
     * @var string[]
     */
    protected $listeners = [
        'eventChangeDatePiker' => 'eventChangeDatePiker',
        'eventChangeInput' => 'eventChangeInput',
        'eventToggleChanged' => 'eventChangeInput',
        'eventMultiSelect' => 'eventMultiSelect'
    ];

    private $collection;

    /**
     * Apply checkbox, perPage and search view and theme
     *
     */
    public function setUp()
    {
        $this->showPerPage();
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
     * default full. other: short, min
     * @param string $mode
     * @return $this
     */
    public function showRecordCount($mode = 'full'): PowerGridComponent
    {
        $this->record_count = $mode;
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
            $this->perPage = $perPage;
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
     * @return array
     */
    public function columns(): array
    {
        return [
            Column::add()
                ->title('ID')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Created at')
                ->field('created_at'),
        ];
    }

    /**
     * @return array
     */
    protected function dataSource(): array
    {
        return [];
    }

    public function render()
    {
        $this->columns = $this->columns();
        $this->collection = $this->collection();
        $data = [];

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }

        if (filled($this->collection)) {

            $data = Collection::search($this->collection, $this->search, $this->columns());
            $data = $this->advancedFilter($data);
            $data = $data->sortBy($this->orderBy, SORT_REGULAR, $this->orderAsc);

            if ($data->count()) {
                $data = Collection::paginate($data, ($this->perPage == '0') ? $data->count() : $this->perPage);
            }
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

    /**
     * @param $data
     */
    private function renderView($data)
    {
        $theme = config('livewire-powergrid.theme');
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

        $collection = $this->collection();

        if (!$update) {
            session()->flash('error', $this->updateMessages('error', $data['field']));
        } else {

            $cached = $collection->map(function ($row) use ($data) {
                $field = $data['field'];
                if ($row->id === $data['id']) {
                    $row->{$field} = $data['value'];
                }
                return $row;
            });

            session()->flash('success', $this->updateMessages('success', $data['field']));

            $this->collection($cached);
        }
    }

    /**
     * @return \Illuminate\Support\Collection|mixed
     * @throws \Exception
     */
    public function collection($cached = '')
    {
        if (filled($cached)) {
            \cache()->forget($this->id);
            return \cache()->rememberForever($this->id, function () use ($cached) {
                return $cached;
            });
        }

        $cache = config('livewire-powergrid.cached_data');
        if ($cache) {
            return \cache()->rememberForever($this->id, function () {
                return new \Illuminate\Support\Collection($this->dataSource());
            });
        }
        return new \Illuminate\Support\Collection($this->dataSource());
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
                'status' => __('Custom Field updated successfully!'),
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
}
