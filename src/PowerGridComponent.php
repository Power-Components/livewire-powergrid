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

    use WithPagination, Checkbox, ExportExcel, Filter;

    /**
     * @var
     */
    protected $model;
    /**
     * @var array
     */
    public array $headers = [];
    /**
     * @var bool
     */
    public bool $search_input = true;
    /**
     * @var string
     */
    public string $search = '';
    /**
     * @var bool
     */
    public bool $perPage_input = true;
    /**
     * @var string
     */
    public string $orderBy = 'id';
    /**
     * @var bool
     */
    public bool $orderAsc = false;

    public $perPage;
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
    public array $icon_sort = [];

    /**
     * @var array
     */
    public array $perPageValues = [10, 25, 50, 100, 0];

    protected $listeners = [
        'inputDatePiker' => 'inputDatePiker',
        'editInput' => 'editInput'
    ];

    /**
     * Apply checkbox, perPage and search view and theme
     *
     */
    public function setUp()
    {
        $this->showCheckBox()->showPerPage()->showSearchInput();
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
     * @param int $perPage
     * @return $this
     */
    public function showPerPage( int $perPage = 10 ): PowerGridComponent
    {
        if (\Str::contains($perPage, $this->perPageValues)) {
            $this->perPage_input = true;
            $this->perPage = $perPage;
        }
        return $this;
    }

    public function mount()
    {
        $this->model = new \Illuminate\Support\Collection($this->dataSource());

        $this->columns = $this->columns();

        $this->paginationTheme = config('livewire-powergrid.theme');

        $this->renderFilter();

        $this->headerIcons();

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
                ->title('Criado em')
                ->field('created_at'),
        ];
    }

    protected function dataSource(): array
    {
        return [];
    }

    public function render()
    {
        $this->model = new \Illuminate\Support\Collection($this->dataSource());

        $this->columns = $this->columns();

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }

        $data = [];

        if (filled($this->model)) {

            $data = Collection::search($this->model, $this->search, $this->columns());
            $data = $this->advancedFilter($data);
            $data = $data->sortBy($this->orderBy, SORT_REGULAR, $this->orderAsc);

            if ($data->count()) {
                $data = Collection::paginate($data, ($this->perPage == '0') ? $data->count(): $this->perPage);
            }
        }

        return $this->renderView($data);

    }

    /**
     * @param $field
     */
    public function setOrder( $field )
    {
        if (!empty($field)) {
            $this->orderBy = $field;
            $this->headerIcons();
            if ($this->orderAsc === true) {
                $this->orderAsc = false;
                $this->icon_sort[$field] = 'M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293V3.5zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1h-1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1h-3zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1h-5zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5z';
            } else {
                $this->orderAsc = true;
                $this->icon_sort[$field] = 'M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.498.498 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707V12.5zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1z';
            }
        }
    }

    private function headerIcons()
    {
        foreach ($this->columns() as $column) {
            $field = $column->field;
            $this->icon_sort[$field] = 'M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293V3.5zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1h-1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1h-3zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1h-5zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5z';
        }
    }

    private function renderView( $data )
    {
        $theme = config('livewire-powergrid.theme');
        $version = config('livewire-powergrid.theme_versions')[$theme];

        return view('livewire-powergrid::' . $theme . '.' . $version . '.table', [
            'data' => $data
        ]);
    }

    public function editInput( $data )
    {
        $update = $this->update($data);
        $this->dispatchBrowserEvent('onUpdateInput', [
            'data' => $data,
            'success' => $update
        ]);
    }

    public function update(array $data ): bool
    {
        return false;
    }

}
