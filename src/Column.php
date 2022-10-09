<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\{Collection, Str};

final class Column
{
    public string $title = '';

    public string $field = '';

    public string $headerClass = '';

    public string $headerStyle = '';

    public string $bodyClass = '';

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

    public array $sum = [
        'header' => false,
        'footer' => false,
    ];

    public array $count = [
        'header' => false,
        'footer' => false,
    ];

    public array $avg = [
        'header' => false,
        'footer' => false,
    ];

    public array $min = [
        'header' => false,
        'footer' => false,
    ];

    public array $max = [
        'header' => false,
        'footer' => false,
    ];

    public array $inputs = [];

    /**
     *
     * @var array<string, array<int, string>|bool> $toggleable
     */
    public array $toggleable = [];

    /**
     *
     * @var array<string, bool|string> $clickToCopy
     */
    public array $clickToCopy = [];

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
     * Adds title
     *
     */
    public function title(string $title): Column
    {
        $this->title = $title;

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
        $this->searchable();

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
        int $rounded = 2
    ): Column {
        $this->sum['label']   = $label;
        $this->sum['header']  = $header;
        $this->sum['footer']  = $footer;
        $this->sum['rounded'] = $rounded;

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
        int $rounded = 2
    ): Column {
        $this->avg['label']   = $label;
        $this->avg['header']  = $header;
        $this->avg['footer']  = $footer;
        $this->avg['rounded'] = $rounded;

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
        int $rounded = 2
    ): Column {
        $this->min['label']   = $label;
        $this->min['header']  = $header;
        $this->min['footer']  = $footer;
        $this->min['rounded'] = $rounded;

        return $this;
    }

    /**
     * Display Column Maximum Summary
     *
     */
    public function withMax(
        string $label = 'Max',
        bool $header = true,
        bool $footer = true,
        int $rounded = 2
    ): Column {
        $this->max['label']   = $label;
        $this->max['header']  = $header;
        $this->max['footer']  = $footer;
        $this->max['rounded'] = $rounded;

        return $this;
    }

    /**
     * Field in the database
     *
     */
    public function field(string $field, string $dataField = ''): Column
    {
        $this->field = $field;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

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
     * Input Select Filter
     *
     */
    public function makeInputSelect(
        Collection $datasource,
        string $displayField,
        string $dataField = null,
        array $settings = []
    ): Column {
        $this->editable                         = [];
        $this->inputs['select']['data_source']  = $datasource;
        $this->inputs['select']['displayField'] = $displayField;
        $this->inputs['select']['dataField']    = $dataField         ?? $displayField;
        $this->inputs['select']['class']        = $settings['class'] ?? '';

        return $this;
    }

    /**
     * Input Multi-Select Filter
     *
     */
    public function makeInputMultiSelect(
        Collection $datasource,
        string $optionText,
        string $dataField = null,
        string $optionValue = 'id'
    ): Column {
        $this->editable                              = [];
        $this->inputs['multi_select']['data_source'] = $datasource;
        $this->inputs['multi_select']['text']        = $optionText ?: $optionValue;
        $this->inputs['multi_select']['value']       = $optionValue;
        $this->inputs['multi_select']['dataField']   = $dataField;

        return $this;
    }

    /**
     * Filter Datepicker
     *
    */
    public function makeInputDatePicker(
        string $dataField = '',
        array $settings = [],
        string $classAttr = ''
    ): Column {
        $this->inputs['date_picker']['enabled'] = true;
        $this->inputs['date_picker']['class']   = $classAttr;
        $this->inputs['date_picker']['config']  = $settings;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Filter Enum - ^php8.1
     *
     */
    public function makeInputEnumSelect(array $enumCases, string $dataField = null, array $settings = []): Column
    {
        $displayField = 'value';

        $dataSource = collect($enumCases)->map(function ($case) use (&$displayField) {
            $option = (array) $case;

            if (method_exists($case, 'labelPowergridFilter')) {
                $option['name'] = $case->labelPowergridFilter();
                $displayField   = 'name';
            }

            return $option;
        });

        $dataField ??= Str::snake(class_basename($enumCases[0]));

        return $this->makeInputSelect($dataSource, $displayField, $dataField, $settings);
    }

    /**
     * Adds Edit on click to a column
     *
     */
    public function editOnClick(bool $hasPermission = true, string $dataField = '', string $fallback = null, bool $saveOnMouseOut = false): Column
    {
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

    /**
     * Add Input Number Range
     */
    public function makeInputRange(
        string $dataField = '',
    ): Column {
        $this->inputs['number']['enabled']   = true;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Add Input Text
     */
    public function makeInputText(string $dataField = ''): Column
    {
        $this->inputs['input_text']['enabled'] = true;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clickToCopy(bool $hasPermission, string $label = 'copy'): Column
    {
        $this->clickToCopy = [
            'enabled' => $hasPermission,
            'label'   => $label,
        ];

        return $this;
    }

    /**
     * Add Boolean Filter
     */
    public function makeBooleanFilter(
        string $dataField = '',
        string $trueLabel = 'Yes',
        string $falseLabel = 'No',
        array $settings = []
    ): Column {
        $this->inputs['boolean']['enabled']     = true;
        $this->inputs['boolean']['true_label']  = $trueLabel;
        $this->inputs['boolean']['false_label'] = $falseLabel;
        $this->inputs['boolean']['class']       = $settings['class'] ?? '';
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }
}
