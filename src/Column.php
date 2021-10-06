<?php

namespace PowerComponents\LivewirePowerGrid;

class Column
{
    public string $title = '';

    public bool $searchable = false;

    public bool $sortable = false;

    public string $field = '';

    public string $headerClass = '';

    public string $headerStyle = '';

    public string $bodyClass = '';

    public string $bodyStyle = '';

    public bool $hidden = false;

    public bool $visibleInExport = true;

    public array $inputs = [];

    public bool $editable = false;

    public array $toggleable = [];

    public array $clickToCopy = [];

    public string $dataField = '';

    public string $placeholder = '';

    /**
     * @return static
     */
    public static function add()
    {
        return new static();
    }

    /**
     * Column title representing a field
     *
     * @param string $title
     * @return $this
     */
    public function title( string $title ): Column
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Placeholder
     *
     * @param string $placeholder
     * @return $this
     */
    public function placeholder( string $placeholder ): Column
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
        $this->searchable = true;

        return $this;
    }

    /**
     * Will enable the column for sort
     *
     * @return $this
     */
    public function sortable(): Column
    {
        $this->sortable = true;

        return $this;
    }

    /**
     * Field name in the database
     *
     * @param string $field
     * @return $this
     */
    public function field( string $field ): Column
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Class html tag header table
     *
     * @param string $classAttr
     * @param string $styleAttr
     * @return $this
     */
    public function headerAttribute( string $classAttr = '', string $styleAttr = '' ): Column
    {
        $this->headerClass = $classAttr;
        $this->headerStyle = $styleAttr;

        return $this;
    }

    /**
     * Class html tag body table
     *
     * @param string $classAttr
     * @param string $styleAttr
     * @return $this
     */
    public function bodyAttribute( string $classAttr = '', string $styleAttr = '' ): Column
    {
        $this->bodyClass = $classAttr;
        $this->bodyStyle = $styleAttr;

        return $this;
    }

    public function hidden(): Column
    {
        $this->hidden = true;

        return $this;
    }

    public function visibleInExport( bool $visible ): Column
    {
        $this->visibleInExport   = $visible;
        $this->searchable        = false;

        return $this;
    }

    /**
     * @param $datasource
     * @param string $displayField
     * @param string $dataField
     * @param array $settings
     * @return $this
     */
    public function makeInputSelect( $datasource, string $displayField, string $dataField, array $settings = [] ): Column
    {
        $this->editable                          = false;
        $this->inputs['select']['data_source']   = $datasource;
        $this->inputs['select']['display_field'] = $displayField;
        $this->inputs['select']['data_field']    = $dataField;
        $this->inputs['select']['class']         = $settings['class'] ?? '';
        $this->inputs['select']['live-search']   = $settings['live-search'] ?? true;

        return $this;
    }

    /**
     * @param $datasource
     * @param string $displayField
     * @param string $dataField
     * @return $this
     */
    public function makeInputMultiSelect( $datasource, string $displayField, string $dataField ): Column
    {
        $this->editable                                = false;
        $this->inputs['multi_select']['data_source']   = $datasource;
        $this->inputs['multi_select']['display_field'] = $displayField;
        $this->inputs['multi_select']['data_field']    = $dataField;
        $this->inputs['multi_select']['live-search']   = $settings['live-search'] ?? true;

        return $this;
    }

    /**
     * @param string $dataField
     * @param array $settings [
     * 'only_future' => true,
     * 'no_weekends' => true
     * ]
     * @param string $classAttr
     * @return Column
     */
    public function makeInputDatePicker( string $dataField, array $settings = [], string $classAttr = '' ): Column
    {
        $this->inputs['date_picker']['enabled'] = true;
        $this->inputs['date_picker']['class']   = $classAttr;
        $this->inputs['date_picker']['config']  = $settings;
        $this->dataField                        = $dataField;

        return $this;
    }

    /**
     * Adds Edit on click to a column
     *
     * @param bool $hasPermission
     * @return Column
     */
    public function editOnClick( bool $hasPermission = true ): Column
    {
        $this->editable = $hasPermission;

        return $this;
    }

    /**
     * Adds Toggle to a column
     *
     * @param bool $hasPermission
     * @param string $trueLabel Label for true
     * @param string $falseLabel Label for false
     * @return Column
     */
    public function toggleable(bool $hasPermission = true, string $trueLabel = 'Yes', string $falseLabel = 'No'): Column
    {
        $this->editable   = false;
        $this->toggleable = [
            'enabled' => $hasPermission,
            'default' => [$trueLabel,  $falseLabel]
        ];

        return $this;
    }

    /**
     * @param string $dataField
     * @param string $thousands
     * @param string $decimal
     * @return $this
     */
    public function makeInputRange( string $dataField = '', string $thousands = '', string $decimal = '' ): Column
    {
        $this->inputs['number']['enabled']   = true;
        $this->inputs['number']['decimal']   = $decimal;
        $this->inputs['number']['thousands'] = $thousands;
        $this->dataField                     = $dataField;

        return $this;
    }

    /**
     * @param string $dataField
     * @return $this
     */
    public function makeInputText( string $dataField = '' ): Column
    {
        $this->inputs['input_text']['enabled'] = true;
        $this->dataField                       = $dataField;

        return $this;
    }

    /**
     * @param $hasPermission
     * @param string $label
     * @return $this
     */
    public function clickToCopy( $hasPermission, string $label = 'copy' ): Column
    {
        $this->clickToCopy = [
            'enabled' => $hasPermission,
            'label'   => $label
        ];

        return $this;
    }

    /**
     * @param string $dataField
     * @param string $trueLabel Label for true
     * @param string $falseLabel Label for false
     * @param array $settings Settings
     * @return $this
     */
    public function makeBooleanFilter(string $dataField = '' , string $trueLabel = 'Yes', string $falseLabel = 'No', array $settings = []): Column
    {
        $this->inputs['boolean_filter']['enabled']     = true;
        $this->inputs['boolean_filter']['true_label']  = $trueLabel;
        $this->inputs['boolean_filter']['false_label'] = $falseLabel;
        $this->inputs['boolean_filter']['class']       = $settings['class'] ?? '';
        $this->inputs['boolean_filter']['live-search'] = $settings['live-search'] ?? true;
        $this->dataField                               = $dataField;

        return $this;
    }
}
