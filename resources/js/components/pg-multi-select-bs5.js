export default (params) => ({
    dataField: params.dataField ?? null,
    tableName: params.tableName ?? null,
    init() {
        const _this = this;
        const field = this.dataField
        const element = '[x-ref="select_picker_'+field+'"]';

        $(element).selectpicker();

        document.addEventListener('DOMContentLoaded', () => {
            Livewire.hook('message.processed', (message, component) => {
                $(element).selectpicker()
            })
        })

        $(element).on('change', function () {
            const selected = $(this).find("option:selected");
            const arrSelected = [];
            selected.each(function () {
                arrSelected.push($(this).val());
            });

            window.livewire.emit('pg:multiSelect-' + _this.tableName, {
                id: field,
                values: arrSelected
            })
            $(element).selectpicker('refresh');
        });
    },
})
