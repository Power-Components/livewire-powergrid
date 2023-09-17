<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\{Actions,
    Checkbox,
    Cols,
    Editable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    Footer,
    Radio,
    SearchBox,
    Table};

class Tailwind extends ThemeBase
{
    public string $name = 'tailwind';

    public function table(): Table
    {
        return Theme::table('min-w-full dark:bg-slate-800')
            ->div('my-3 rounded-lg relative border border-pg-primary-200 dark:bg-pg-primary-700 dark:border-pg-primary-600')
            ->thead('shadow-sm rounded-t-lg bg-pg-primary-200 dark:bg-gray-900')
            ->thAction('!font-bold')
            ->tdAction('')
            ->tr('')
            ->trFilters('bg-white shadow-sm dark:bg-pg-primary-800')
            ->th('font-semibold px-2 pr-4 py-3 text-left text-xs font-semibold text-pg-primary-700 tracking-wider whitespace-nowrap dark:text-pg-primary-300')
            ->tbody('text-pg-primary-800')
            ->trBody('border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700')
            ->tdBody('pl-[19px] px-3 py-2 whitespace-nowrap dark:text-pg-primary-200')
            ->tdBodyEmpty('px-3 py-2 whitespace-nowrap dark:text-pg-primary-200')
            ->trBodyClassTotalColumns('!bg-red-800')
            ->tdBodyTotalColumns('px-3 py-2 whitespace-nowrap dark:text-pg-primary-200 text-sm text-pg-primary-600 text-right space-y-2');
    }

    public function footer(): Footer
    {
        return Theme::footer()
            ->view($this->root() . '.footer')
            ->select('block appearance-none bg-pg-primary-50 border border-pg-primary-300 text-pg-primary-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 dark:bg-pg-primary-700 dark:text-pg-primary-200 dark:placeholder-pg-primary-200 dark:border-pg-primary-500');
    }

    public function actions(): Actions
    {
        return Theme::actions()
            ->headerBtn('block w-full bg-pg-primary-50 text-pg-primary-700 border border-pg-primary-200 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-600 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600')
            ->rowsBtn('focus:outline-none text-sm py-2.5 px-5 rounded border');
    }

    public function cols(): Cols
    {
        return Theme::cols()
            ->div('select-none')
            ->clearFilter('', '');
    }

    public function editable(): Editable
    {
        return Theme::editable()
            ->view($this->root() . '.editable')
            ->span('flex justify-between')
            ->input('dark:bg-pg-primary-800 bg-pg-primary-50 text-black-700 border border-pg-primary-200 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-200 dark:bg-pg-primary-600 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600 shadow-md');
    }

    public function checkbox(): Checkbox
    {
        return Theme::checkbox()
            ->th('px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider')
            ->label('flex items-center space-x-3')
            ->input('h-4 w-4');
    }

    public function radio(): Radio
    {
        return Theme::radio()
            ->th('px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider')
            ->label('flex items-center space-x-3')
            ->input('form-radio rounded-full transition ease-in-out duration-100');
    }

    public function filterBoolean(): FilterBoolean
    {
        return Theme::filterBoolean()
            ->view($this->root() . '.filters.boolean')
            ->base('min-w-[5rem]')
            ->select('appearance-none block mt-1 mb-1 bg-white border border-pg-primary-300 text-pg-primary-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 w-full dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600', 'max-width: 370px');
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return Theme::filterDatePicker()
            ->base()
            ->view($this->root() . '.filters.date-picker')
            ->input('flatpickr flatpickr-input block my-1 bg-white border border-pg-primary-300 text-pg-primary-700 py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 w-full placeholder-pg-primary-400 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600');
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return Theme::filterMultiSelect()
            ->base('inline-block relative w-full')
            ->select('mt-1')
            ->view($this->root() . '.filters.multi-select');
    }

    public function filterNumber(): FilterNumber
    {
        return Theme::filterNumber()
            ->view($this->root() . '.filters.number')
            ->input('block bg-white border border-pg-primary-300 text-pg-primary-700 py-2 px-3 rounded leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 w-full min-w-[5rem] placeholder-pg-primary-400 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600');
    }

    public function filterSelect(): FilterSelect
    {
        return Theme::filterSelect()
            ->view($this->root() . '.filters.select')
            ->base('min-w-[9.5rem]')
            ->select('appearance-none block bg-white border border-pg-primary-300 text-pg-primary-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 w-full dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600');
    }

    public function filterInputText(): FilterInputText
    {
        return Theme::filterInputText()
            ->view($this->root() . '.filters.input-text')
            ->base('min-w-[9.5rem]')
            ->select('appearance-none block bg-white border border-pg-primary-300 text-pg-primary-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 w-full placeholder-pg-primary-400 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600')
            ->input('w-full block bg-white text-pg-primary-700 border border-pg-primary-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 placeholder-pg-primary-400 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600');
    }

    public function searchBox(): SearchBox
    {
        return Theme::searchBox()
            ->input('placeholder-pg-primary-400 text-sm pl-[36px] block w-full float-right bg-white text-pg-primary-700 border border-pg-primary-300 rounded-lg py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-pg-primary-500 pl-10 dark:bg-pg-primary-800 dark:text-pg-primary-200 dark:placeholder-pg-primary-300 dark:border-pg-primary-600')
            ->iconClose('text-pg-primary-400 dark:text-pg-primary-200')
            ->iconSearch('text-pg-primary-300 mr-2 w-5 h-5 dark:text-pg-primary-200');
    }
}
