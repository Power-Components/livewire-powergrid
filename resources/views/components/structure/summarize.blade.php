@props([
    'sum' => null,
    'labelSum' => null,
    'count' => null,
    'labelCount' => null,
    'min' => null,
    'labelMin' => null,
    'max' => null,
    'labelMax' => null,
    'avg' => null,
    'labelAvg' => null,
    'custom' => [],
])
<div>
    @if ($sum)
        <span>{!! $labelSum !!}: {{ $sum }}</span>
        <br>
    @endif

    @if ($count)
        <span>{!! $labelCount !!}: {{ $count }}</span>
        <br>
    @endif
    @if ($min)
        <span>{!! $labelMin !!}: {{ $min }}</span>
        <br>
    @endif

    @if ($max)
        <span>{!! $labelMax !!}: {{ $max }}</span>
        <br>
    @endif

    @if ($avg)
        <span>{!! $labelAvg !!}: {{ $avg }}</span>
        <br>
    @endif

    @foreach ($custom as $customSummary)
        @if (! is_null($customSummary['value']))
            <span>{!! $customSummary['label'] !!}: {{ $customSummary['value'] }}</span>
            <br>
        @endif
    @endforeach
</div>
