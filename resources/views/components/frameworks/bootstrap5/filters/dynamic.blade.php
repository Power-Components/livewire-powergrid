@inject('componentAttributeBag','\Illuminate\View\ComponentAttributeBag')
@props([
    'theme' => '',
    'input' => null,
])
@php
    $attributes = new $componentAttributeBag($input['attributes']);
@endphp
<div wire:key="pg-dynamic-{{ md5(json_encode($input)) }}">
    <x-dynamic-component :component="$input['component']" :attributes="$attributes" />
</div>
