<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Facades\Cache;
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
    public array $perPageValues = [10, 25, 50, 100, 0];

    public $sortIcon = '&#8597;';

    public $sortAscIcon = '&#8593;';

    public $sortDescIcon = '&#8595;';

    protected $listeners = [
        'inputDatePiker' => 'inputDatePiker',
        'inputChanged' => 'inputChanged',
        'toggleChanged' => 'inputChanged'
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
        $this->columns = $this->columns();
        $data = [];

        if (method_exists($this, 'initActions')) {
            $this->initActions();
        }

        if (filled($this->collection())) {

            $data = Collection::search($this->collection(), $this->search, $this->columns());
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
    public function setOrder( $field )
    {
        if ($this->orderBy === $field) {
            $this->orderAsc = !$this->orderAsc;
        }
        $this->orderBy = $field;
    }

    private function renderView( $data )
    {
        $theme = config('livewire-powergrid.theme');
        $version = config('livewire-powergrid.theme_versions')[$theme];

        return view('livewire-powergrid::' . $theme . '.' . $version . '.table', [
            'data' => $data
        ]);
    }

    public function inputChanged( $data )
    {
        $update = $this->update($data);
        $this->collection();
        if (!$update) {
            session()->flash('error', trans('livewire-powergrid::datatable.alert.error'));
        } else {
            session()->flash('success', trans('livewire-powergrid::datatable.alert.success'));
        }
    }

    private function collection()
    {
        $cache = (bool) config('livewire-powergrid.cached_data');
        $collection = new \Illuminate\Support\Collection($this->dataSource());
        if ($cache) {
            return Cache::rememberForever($this->id, function () use ( $collection ) {
                return $collection;
            });
        }
        return $collection;
    }

    public function update( array $data ): bool
    {
        return false;
    }


}
