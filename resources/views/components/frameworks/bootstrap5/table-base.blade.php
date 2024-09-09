@php
    $columns = collect($columns)->map(function ($column) {
        return data_forget($column, 'rawQueries');
    });
@endphp
<div @if ($deferLoading) wire:init="fetchDatasource" @endif>
    <div class="col-md-12">
        @include(theme_style($theme, 'layout.header'), [
            'enabledFilters' => $enabledFilters,
        ])
    </div>
    <div
        class="{{ theme_style($theme, 'table.layout.div') }}"
    >
        @include($table)
    </div>
    <div class="row">
        <div class="col-12 overflow-auto">
            @include(theme_style($theme, 'footer.view'))
        </div>
    </div>
</div>
