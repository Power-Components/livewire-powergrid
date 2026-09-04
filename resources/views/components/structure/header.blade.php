@props([
    '__partial' => null,
])

@php
    $__partial = $__partial ?? $this;
    $tableName = $__partial->tableName;
    $setUp = $__partial->setUp;
@endphp

<div
    class="pg-header-container"
    wire:partial="pg-header-{{ $tableName }}"
    wire:key="header-{{ $tableName }}"
>
    @includeIf(data_get($setUp, 'header.includeViewOnTop'), ['__partial' => $__partial])
    <header class="pg-header {{ theme('header.layout.container') }}">
        <div class="pg-header-sub {{ theme('header.layout.sub_container') }}">
            <div class="pg-actions {{ theme('header.layout.actions_container') }}">
                 {!! $__partial->renderHeaderActions() !!}
                 {!! $__partial->renderPluginZone('header') !!}
                 @includeIf(theme_view('header.toggle-columns'), ['__partial' => $__partial])
            </div>

            <div class="pg-search {{ theme('header.search_box.container') }}">
                @includeIf(theme_view('header.search'), ['__partial' => $__partial])
            </div>
        </div>
    </header>
    {!! $__partial->renderPluginZone('header.bottom') !!}
    @includeIf(data_get($setUp, 'header.includeViewOnBottom'), ['__partial' => $__partial])
</div>
