<?php

namespace PowerComponents\LivewirePowerGrid;

final class Column implements \Livewire\Wireable
{
    public string $title = '';

    public string $field = '';

    public string $headerClass = '';

    public string $headerStyle = '';

    public string $bodyClass = '';

    public string $contentClassField = '';

    public string|array $contentClasses = [];

    public string $bodyStyle = '';

    public string $dataField = '';

    public string $placeholder = '';

    public bool $hidden = false;

    public bool $forceHidden = false;

    public ?bool $visibleInExport = null;

    public array $editable = [];

    public bool $searchable = false;

    public string $searchableRaw = '';

    public bool $sortable = false;

    public array $summarize = [];

    public array $sum = [
        'header' => false,
        'footer' => false,
        'label'  => null,
    ];

    public array $count = [
        'header' => false,
        'footer' => false,
        'label'  => null,
    ];

    public array $avg = [
        'header' => false,
        'footer' => false,
        'label'  => null,
    ];

    public array $min = [
        'header' => false,
        'footer' => false,
        'label'  => null,
    ];

    public array $max = [
        'header' => false,
        'footer' => false,
        'label'  => null,
    ];

    public mixed $filters = null;

    public bool $isAction = false;

    /**
     *
     * @var array<string, array<int, string>|bool> $toggleable
     */
    public array $toggleable = [];

    public bool $index = false;

    public bool $fixedOnResponsive = false;

    /**
     * Adds a new Column
     *
     * @return self
     */
    public static function add(): self
    {
        return new Column();
    }

    /**
     * Make a new Column
     */
    public static function make(string $title, string $field, string $dataField = ''): self
    {
        return (new static())
            ->title($title)
            ->field($field, $dataField);
    }

    /**
     * Make a new action
     */
    public static function action(string $title): self
    {
        return (new static())
            ->title($title)
            ->isAction();
    }

    public function isAction(): Column
    {
        $this->isAction = true;

        return $this;
    }

    /**
     * Adds title
     *
     */
    public function title(string $title): Column
    {
        $this->title = $title;

        return $this;
    }

    public function fixedOnResponsive(): Column
    {
        $this->fixedOnResponsive = true;

        return $this;
    }

    /**
     * Adds index ($loop->index)
     *
     */
    public function index(): Column
    {
        $this->index = true;

        return $this;
    }

    /**
     * Adds placeholder
     *
     */
    public function placeholder(string $placeholder): Column
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Makes the column searchable
     *
     */
    public function searchable(): Column
    {
        $this->searchable = true;

        return $this;
    }

    /**
     * Makes the column searchable with SQL Raw
     *
    */
    public function searchableRaw(string $sql): Column
    {
        $this->searchableRaw = $sql;

        return $this;
    }

    /**
     * Adds sort to the column header
     *
     * @return Column
     */
    public function sortable(): Column
    {
        $this->sortable = true;

        return $this;
    }

    /**
     * Display Column Sum Summary
     *
     */
    public function withSum(
        string $label = 'Sum',
        bool $header = true,
        bool $footer = true,
    ): Column {
        $this->sum['label']  = $label;
        $this->sum['header'] = $header;
        $this->sum['footer'] = $footer;

        return $this;
    }

    /**
     * Display Column Count Summary
     *
     */
    public function withCount(
        string $label = 'Count',
        bool $header = true,
        bool $footer = true
    ): Column {
        $this->count['label']  = $label;
        $this->count['header'] = $header;
        $this->count['footer'] = $footer;

        return $this;
    }

    /**
     * Display Column Average Summary
     *
     */
    public function withAvg(
        string $label = 'Avg',
        bool $header = true,
        bool $footer = true,
    ): Column {
        $this->avg['label']  = $label;
        $this->avg['header'] = $header;
        $this->avg['footer'] = $footer;

        return $this;
    }

    /**
     * Display Column Minimum Summary
     *
     */
    public function withMin(
        string $label = 'Min',
        bool $header = true,
        bool $footer = true,
    ): Column {
        $this->min['label']  = $label;
        $this->min['header'] = $header;
        $this->min['footer'] = $footer;

        return $this;
    }

    /**
     * Display Column Maximum Summary
     *
     */
    public function withMax(
        string $label = 'Max',
        bool $header = true,
        bool $footer = true
    ): Column {
        $this->max['label']  = $label;
        $this->max['header'] = $header;
        $this->max['footer'] = $footer;

        return $this;
    }

    /**
     * Field in the database
     *
     */
    public function field(string $field, string $dataField = ''): Column
    {
        $this->field = $field;

        $this->dataField = filled($dataField) ? $dataField : $field;

        return $this;
    }

    /**
    * Class html tag header table
     *
     */
    public function headerAttribute(string $classAttr = '', string $styleAttr = ''): Column
    {
        $this->headerClass = $classAttr;
        $this->headerStyle = $styleAttr;

        return $this;
    }

    /**
    * Class html tag body table
     *
     */
    public function bodyAttribute(string $classAttr = '', string $styleAttr = ''): Column
    {
        $this->bodyClass = $classAttr;
        $this->bodyStyle = $styleAttr;

        return $this;
    }

    /**
    * Hide the column
     *
     */
    public function hidden(bool $isHidden = true, bool $isForceHidden = true): Column
    {
        $this->hidden      = $isHidden;
        $this->forceHidden = $isForceHidden;

        return $this;
    }

    public function visibleInExport(?bool $visible): Column
    {
        $this->visibleInExport = $visible;

        return $this;
    }

    /**
     * Adds Edit on click to a column
     *
     */
    public function editOnClick(
        bool $hasPermission = true,
        string $dataField = '',
        string $fallback = null,
        bool $saveOnMouseOut = false
    ): Column {
        $this->editable = [
            'hasPermission'  => $hasPermission,
            'fallback'       => $fallback,
            'saveOnMouseOut' => $saveOnMouseOut,
        ];

        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Adds Toggle to a column
     *
     */
    public function toggleable(
        bool $hasPermission = true,
        string $trueLabel = 'Yes',
        string $falseLabel = 'No'
    ): Column {
        $this->editable   = [];
        $this->toggleable = [
            'enabled' => $hasPermission,
            'default' => [$trueLabel,  $falseLabel],
        ];

        return $this;
    }

    public function contentClassField(string $dataField = ''): Column
    {
        $this->contentClassField = $dataField;

        return $this;
    }

    public function contentClasses(string|array $contentClasses): Column
    {
        $this->contentClasses = $contentClasses;

        return $this;
    }

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
