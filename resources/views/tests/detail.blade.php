<div class="p-2 bg-white border border-zinc-200">
    <div>Id {{ $id }}</div>
    <div>Options @json($options)</div>
    @php
        \Illuminate\Support\Facades\Log::info($id);
    @endphp
</div>
