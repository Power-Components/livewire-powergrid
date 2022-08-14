@inject('componentAttributeBag','\Illuminate\View\ComponentAttributeBag')
@props([
    'theme' => '',
    'inline' => null,
    'multiSelect' => null,
])
@php
    $attributes = new $componentAttributeBag($multiSelect['attributes']);
@endphp
<div wire:key="pg-dynamic-{{ md5(json_encode($multiSelect)) }}">
    <x-dynamic-component :component="$multiSelect['component']" :attributes="$attributes" />
</div>
