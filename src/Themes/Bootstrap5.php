<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Bootstrap5 extends ThemeBase
{
    public string $name = 'bootstrap5';

    public static function paginationTheme(): string
    {
        return 'bootstrap';
    }

    public function table(): Components\Table
    {
        return Theme::table('table table-bordered table-hover table-striped table-checkable table-highlight-head mb-2')
            ->thead('')
            ->tr('')
            ->th('', 'white-space: nowrap;min-width: 50px;padding-left: 15px;font-size: 0.75rem;color: #6b6a6a;padding-top: 8px;padding-bottom: 8px;')
            ->tbody('')
            ->trBody('')
            ->tdBody('', 'vertical-align: middle; line-height: normal;');
    }

    public function cols(): Components\Cols
    {
        return Theme::cols()
            ->div('')
            ->clearFilter('', 'color: #c30707; cursor:pointer; float: right;');
    }

    public function footer(): Components\Footer
    {
        return Theme::footer()
            ->view($this->root() . '.footer')
            ->select('');
    }

    public function actions(): Components\Actions
    {
        return Theme::actions()
            ->tdBody('text-center')
            ->rowsBtn('');
    }

    public static function styles(): string
    {
        return 'bootstrap';
    }

    public function toggleable(): Components\Toggleable
    {
        return Theme::toggleable()
            ->view($this->root() . '.toggleable');
    }

    public function editable(): Components\Editable
    {
        return Theme::editable()
            ->view($this->root() . '.editable')
            ->span('d-flex justify-content-between')
            ->button('width: 100%;text-align: left;border: 0;padding: 4px;background: none')
            ->input('form-control');
    }

    public function checkbox(): Components\Checkbox
    {
        return Theme::checkbox()
            ->th('', 'font-size: 1rem !important;text-align:center')
            ->div('form-check')
            ->label('form-check-label')
            ->input('form-check-input');
    }

    public function filterBoolean(): Components\FilterBoolean
    {
        return Theme::filterBoolean()
            ->input('form-control')
            ->relativeDiv('d-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterDatePicker(): Components\FilterDatePicker
    {
        return Theme::filterDatePicker()
            ->input('form-control')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterMultiSelect(): Components\FilterMultiSelect
    {
        return Theme::filterMultiSelect()
            ->view($this->root() . '.multi-select')
            ->input('')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterNumber(): Components\FilterNumber
    {
        return Theme::filterNumber()
            ->input('form-control')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterSelect(): Components\FilterSelect
    {
        return Theme::filterSelect()
            ->input('form-control')
            ->relativeDiv('d-none')
            ->divNotInline('')
            ->divInline('');
    }

    public function filterInputText(): Components\FilterInputText
    {
        return Theme::filterInputText()
            ->select('form-control mb-1', 'd-none')
            ->input('form-control')
            ->divNotInline('')
            ->divInline('');
    }
}
