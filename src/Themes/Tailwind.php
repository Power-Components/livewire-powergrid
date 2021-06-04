<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Components\{Checkbox,
    Editable,
    FilterBoolean,
    FilterDatePicker,
    FilterInputText,
    FilterMultiSelect,
    FilterNumber,
    FilterSelect,
    PerPage,
    Table,
    Layout,
    Toggleable
};

class Tailwind extends ThemeBase
{
    public function __construct()
    {
        self::$scripts = 'livewire-powergrid::components\\frameworks\\tailwind\\scripts';
        self::$styles  = 'livewire-powergrid::components\\frameworks\\tailwind\\styles';
    }

    public function table(): Table
    {
        return Theme::table('min-w-full divide-y divide-gray-200')
            ->thead('bg-gray-50')
            ->tr('')
            ->th('px-2 pr-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap')
            ->tbody('text-gray-800')
            ->trBody('border-b border-gray-200 hover:bg-gray-100')
            ->tdBody('px-3 py-2 whitespace-nowrap');
    }

    public function perPage(): PerPage
    {
        return Theme::perPage()
            ->view('livewire-powergrid::components.frameworks.tailwind.footer')
            ->selectClass('block appearance-none bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500');
    }

    public function toggleable(): Toggleable
    {
        return Theme::toggleable()
            ->view('livewire-powergrid::components.frameworks.tailwind.toggleable');
    }

    public function editable(): Editable
    {
        return Theme::editable()
            ->view('livewire-powergrid::components.frameworks.tailwind.editable')
            ->spanClass('flex justify-between');
    }

    public function layout(): Layout
    {
        return Theme::layout()
            ->header('livewire-powergrid::components.frameworks.tailwind.header')
            ->pagination('livewire-powergrid::components.frameworks.tailwind.pagination')
            ->message('livewire-powergrid::components.frameworks.tailwind.message')
            ->footer('livewire-powergrid::components.frameworks.tailwind.footer');
    }

    public function checkbox(): Checkbox
    {
        return Theme::checkbox()
            ->thClass('px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider')
            ->labelClass('flex items-center space-x-3')
            ->inputClass('form-checkbox h-4 w-4');
    }

    public function filterBoolean(): FilterBoolean
    {
        return Theme::filterBoolean()
            ->inputClass('appearance-none livewire_powergrid_input block mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active')
            ->divClassNotInline('pt-2 p-2')
            ->divStyle('max-width: 370px')
            ->divClassInline('');
    }

    public function filterDatePicker(): FilterDatePicker
    {
        return Theme::filterDatePicker()
            ->inputClass('appearance-none livewire_powergrid_input flatpickr flatpickr-input block mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active')
            ->divClassNotInline('pt-2 p-2')
            ->divClassInline('');
    }

    public function filterMultiSelect(): FilterMultiSelect
    {
        return Theme::filterMultiSelect()
            ->inputClass('appearance-none livewire_powergrid_input block mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active')
            ->divClassNotInline('pt-2 p-2')
            ->divClassInline('pr-6');
    }

    public function filterNumber(): FilterNumber
    {
        return Theme::filterNumber()
            ->inputClass('appearance-none livewire_powergrid_input block mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active')
            ->divClassNotInline('pt-2 p-2')
            ->divClassInline('pr-6');
    }

    public function filterSelect(): FilterSelect
    {
        return Theme::filterSelect()
            ->inputClass('appearance-none livewire_powergrid_input block mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active')
            ->divClassNotInline('pt-2 p-2')
            ->divClassInline('pr-6');
    }

    public function filterInputText(): FilterInputText
    {
        return Theme::filterInputText()
            ->selectClass('appearance-none livewire_powergrid_input block mt-1 mb-1 bg-white-200 border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500 w-full active')
            ->inputClass('mt-2 w-full block bg-white-200 text-gray-700 border border-gray-300 rounded py-2 px-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500')
            ->divClassNotInline('pt-2 p-2')
            ->divClassInline('pr-6');
    }

    public function tableBaseView(): string
    {
        return 'livewire-powergrid::components\\frameworks\\tailwind\\table-base';
    }

}
