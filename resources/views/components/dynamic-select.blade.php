@props([
    'class' => null,
    'options' => [],
    'option-label' => null,
    'option-value' => null,
    'placeholder' => null,
])
<div>
    <div>class: {{ $class }}</div>
    <div>options: {{ json_encode($options) }}</div>
    <div>option-label: {{ $optionLabel }}</div>
    <div>option-value: {{ $optionValue }}</div>
    <div>placeholder: {{ $placeholder }}</div>
</div>
