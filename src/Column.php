<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Collection;

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

    public bool $visibleInExport = true;

    public bool $editable = false;

    public bool $searchable = false;

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
     * @return self
     */
    public static function add(): self
    {
        return new Column();
    }

    /**
     * Column title representing a field
     *
     * @return $this
     */
    public function title(string $title): Column
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Placeholder
     *
     * @return $this
     */
    public function placeholder(string $placeholder): Column
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Will enable the column for search
     *
     * @return $this
     */
    public function searchable(): Column
    {
        $this->searchable       = true;

        return $this;
    }

    /**
     * Will enable the column for sort
     *
     * @return $this
     */
    public function sortable(): Column
    {
        $this->sortable            = true;

        return $this;
    }

    /**
     * Will enable the column for total sum
     *
     * @return $this
     */
    public function withSum(string $label = 'Sum', bool $header = true, bool $footer = true): Column
    {
        $this->sum['label']                = $label;
        $this->sum['header']               = $header;
        $this->sum['footer']               = $footer;

        return $this;
    }

    /**
     * Will enable the column for total count
     *
     * @return $this
     */
    public function withCount(string $label = 'Count', bool $header = true, bool $footer = true): Column
    {
        $this->count['label']                = $label;
        $this->count['header']               = $header;
        $this->count['footer']               = $footer;

        return $this;
    }

    /**
     * Will enable the column for total average
     *
     * @return $this
     */
    public function withAvg(string $label = 'Avg', bool $header = true, bool $footer = true, int $rounded = 2): Column
    {
        $this->avg['label']      = $label;
        $this->avg['header']     = $header;
        $this->avg['footer']     = $footer;
        $this->avg['rounded']    = $rounded;

        return $this;
    }

    /**
     * Field in the database
     *
     * @return $this
     */
    public function field(string $field, string $dataField = ''): Column
    {
        $this->field     = $field;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Class html tag header table
     *
     * @return $this
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
     * @return $this
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
    * @param bool $isHidden default: true
    *
    * @return Column
    */
    public function hidden(bool $isHidden = true): Column
    {
        $this->hidden      = $isHidden;
        $this->forceHidden = $isHidden;

        return $this;
    }

    public function visibleInExport(bool $visible): Column
    {
        $this->visibleInExport   = $visible;
        $this->searchable        = false;

        return $this;
    }

    /**
     * @param array<string, bool> $settings
     * @return $this
     */
    public function makeInputSelect(Collection $datasource, string $displayField, string $dataField, array $settings = []): Column
    {
        $this->editable                          = false;
        $this->inputs['select']['data_source']   = $datasource;
        $this->inputs['select']['display_field'] = $displayField;
        $this->inputs['select']['data_field']    = $dataField;
        $this->inputs['select']['class']         = $settings['class']       ?? '';
        $this->inputs['select']['live-search']   = $settings['live-search'] ?? true;

        return $this;
    }

    /**
     * @param array<string, bool> $settings
     * @return $this
     */
    public function makeInputMultiSelect(Collection $datasource, string $displayField, string $dataField, array $settings = []): Column
    {
        $this->editable                                = false;
        $this->inputs['multi_select']['data_source']   = $datasource;
        $this->inputs['multi_select']['display_field'] = $displayField;
        $this->inputs['multi_select']['data_field']    = $dataField;
        $this->inputs['multi_select']['live-search']   = $settings['live-search'] ?? true;

        return $this;
    }

    /**
     * @param array<string, bool> $settings [only_future', 'no_weekends']
     * @return Column
     */
    public function makeInputDatePicker(string $dataField = '', array $settings = [], string $classAttr = ''): Column
    {
        $this->inputs['date_picker']['enabled'] = true;
        $this->inputs['date_picker']['class']   = $classAttr;
        $this->inputs['date_picker']['config']  = $settings;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Adds Edit on click to a column
     *
     * @return Column
     */
    public function editOnClick(bool $hasPermission = true, string $dataField = ''): Column
    {
        $this->editable  = $hasPermission;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * Adds Toggle to a column
     *
     * @return Column
     */
    public function toggleable(bool $hasPermission = true, string $trueLabel = 'Yes', string $falseLabel = 'No'): Column
    {
        $this->editable   = false;
        $this->toggleable = [
            'enabled' => $hasPermission,
            'default' => [$trueLabel,  $falseLabel],
        ];

        return $this;
    }

    /**
     * @return $this
     */
    public function makeInputRange(string $dataField = '', string $thousands = '', string $decimal = ''): Column
    {
        $this->inputs['number']['enabled']   = true;
        $this->inputs['number']['decimal']   = $decimal;
        $this->inputs['number']['thousands'] = $thousands;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }

    /**
     * @return $this
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
     * @param array<string, string> $settings Settings
     * @return $this
     */
    public function makeBooleanFilter(string $dataField = '', string $trueLabel = 'Yes', string $falseLabel = 'No', array $settings = []): Column
    {
        $this->inputs['boolean_filter']['enabled']     = true;
        $this->inputs['boolean_filter']['true_label']  = $trueLabel;
        $this->inputs['boolean_filter']['false_label'] = $falseLabel;
        $this->inputs['boolean_filter']['class']       = $settings['class']       ?? '';
        $this->inputs['boolean_filter']['live-search'] = $settings['live-search'] ?? true;
        if (filled($dataField)) {
            $this->dataField = $dataField;
        }

        return $this;
    }
}
