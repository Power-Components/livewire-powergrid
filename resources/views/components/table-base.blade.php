@props([
    'theme' => null,
    'readyToLoad' => false,
])
<div x-data="{{ isset($this->setUp['responsive']) ? 'tableResponsive' : '{}' }}">
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

    @isset($this->setUp['responsive'])
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                this.livewire.hook('message.processed', () => {
                    Alpine.nextTick(() => {
                        window.dispatchEvent(
                            new CustomEvent('pg-livewire-request-finished')
                        );
                    })
                })
                this.livewire.hook('message.failed', () => {
                    Alpine.nextTick(() => {
                        window.dispatchEvent(
                            new CustomEvent('pg-livewire-request-finished')
                        );
                    })
                })
            });
        </script>
    @endisset
</div>
