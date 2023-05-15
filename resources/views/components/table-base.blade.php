@props([
    'theme' => null,
    'readyToLoad' => false,
])
<div x-data="tableResponsive">
    <table class="table power-grid-table {{ $theme->tableClass }}"
           style="{{$theme->tableStyle}}">
        <thead class="{{$theme->theadClass}}"
               style="{{$theme->theadStyle}}">
                {{ $header }}
        </thead>
        @if($readyToLoad)
            <tbody class="{{$theme->tbodyClass}}"
                   style="{{$theme->tbodyStyle}}">
            {{ $rows }}
            </tbody>
        @else
            <tbody class="{{$theme->tbodyClass}}"
                   style="{{$theme->tbodyStyle}}">
            {{ $loading }}
            </tbody>
        @endif
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            this.livewire.hook('message.sent', () => {
                window.dispatchEvent(
                    new CustomEvent('loading', { detail: { loading: true }})
                );
            } )
            this.livewire.hook('message.processed', () => {
                window.dispatchEvent(
                    new CustomEvent('loading', { detail: { loading: false }})
                );
            })
            this.livewire.hook('message.failed', () => {
                window.dispatchEvent(
                    new CustomEvent('loading', { detail: { loading: false }})
                );
            })
        });
    </script>
</div>
