<div class="my-custom-export-progress" wire:poll="updateExportProgress">
    Progress: {{ (int) data_get($exportState, 'progress', 0) }}%
    @foreach ((array) data_get($exportState, 'files', []) as $file)
        <a wire:click="downloadExport('{{ $file }}')">{{ $file }}</a>
    @endforeach
</div>
