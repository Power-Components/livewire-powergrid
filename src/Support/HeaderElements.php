<?php

namespace PowerComponents\LivewirePowerGrid\Support;

final class HeaderElements
{
    /**
     * @var array<string, array{path: string, token: string, icon_token: string, class_token: string, icon: string, title: string, label: bool}>
     *
     * path       = where the user configuration lives inside $setUp
     * token      = theme token prefix holding the css classes
     * icon_token = theme token holding the default icon name
     * class_token = theme token holding the css classes applied to the icon
     * icon       = package default icon component
     * title      = default title lang key
     * label      = whether the title is rendered as visible text by default
     */
    public const ELEMENTS = [
        'toggleColumns' => [
            'path' => 'header.elements.toggleColumns',
            'token' => 'header.toggle_columns',
            'icon_token' => 'header.toggle_columns.icon',
            'class_token' => 'header.toggle_columns.icon_class',
            'icon' => 'livewire-powergrid::icons.eye-off',
            'title' => 'livewire-powergrid::datatable.buttons.toggle_columns',
            'label' => false,
        ],
        'softDeletes' => [
            'path' => 'header.elements.softDeletes',
            'token' => 'header.soft_deletes',
            'icon_token' => 'header.soft_deletes.icon',
            'class_token' => 'header.soft_deletes.icon_class',
            'icon' => 'livewire-powergrid::icons.trash',
            'title' => 'livewire-powergrid::datatable.buttons.soft_deletes',
            'label' => false,
        ],
        'search' => [
            'path' => 'header.elements.search',
            'token' => 'header.search_box',
            'icon_token' => 'header.search_box.icon',
            'class_token' => 'header.search_box.icon_search',
            'icon' => 'livewire-powergrid::icons.search',
            'title' => 'livewire-powergrid::datatable.placeholders.search',
            'label' => false,
        ],
        'searchClear' => [
            'path' => 'header.elements.searchClear',
            'token' => 'header.search_box',
            'icon_token' => 'header.search_box.icon_clear',
            'class_token' => 'header.search_box.icon_close',
            'icon' => 'livewire-powergrid::icons.x',
            'title' => 'livewire-powergrid::datatable.buttons.close',
            'label' => false,
        ],
        'filters' => [
            'path' => 'header.elements.filters',
            'token' => 'header.filters',
            'icon_token' => 'header.filters.icon',
            'class_token' => 'header.filters.icon_class',
            'icon' => 'livewire-powergrid::icons.filter',
            'title' => 'livewire-powergrid::datatable.buttons.filter',
            'label' => false,
        ],
        'clearFilters' => [
            'path' => 'header.elements.clearFilters',
            'token' => 'header.enabled_filters',
            'icon_token' => 'header.enabled_filters.icon',
            'class_token' => 'header.enabled_filters.icon_class',
            'icon' => 'livewire-powergrid::icons.x',
            'title' => 'livewire-powergrid::datatable.buttons.clear_all_filters',
            'label' => true,
        ],
        'filterBuilder' => [
            'path' => 'filterBuilder.trigger',
            'token' => 'header.filter_builder',
            'icon_token' => 'header.filter_builder.icon',
            'class_token' => 'header.filter_builder.icon_class',
            'icon' => 'livewire-powergrid::icons.filter',
            'title' => 'livewire-powergrid::datatable.filter_builder.trigger',
            'label' => false,
        ],
        'export' => [
            'path' => 'exportable.trigger',
            'token' => 'header.export',
            'icon_token' => 'header.export.icon',
            'class_token' => 'header.export.icon_class',
            'icon' => 'livewire-powergrid::icons.download',
            'title' => 'livewire-powergrid::datatable.buttons.export',
            'label' => false,
        ],
    ];

    /** @return array{path: string, token: string, icon_token: string, class_token: string, icon: string, title: string, label: bool} */
    public static function definition(string $key): array
    {
        return self::ELEMENTS[$key] ?? [
            'path' => '',
            'token' => '',
            'icon_token' => '',
            'class_token' => '',
            'icon' => '',
            'title' => '',
            'label' => false,
        ];
    }
}
