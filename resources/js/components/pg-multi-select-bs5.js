export default (params) => ({
    dataField: params.dataField ?? null,
    tableName: params.tableName ?? null,
    init() {
        const _this = this;
        const element = '[x-ref="select_picker_'+_this.dataField+'"]';

        $(function () {
            $(element).selectpicker();
        })

        $(element).selectpicker();
        $(element).on('change', function () {
            const selected = $(this).find("option:selected");
            const arrSelected = [];
            selected.each(function () {
                arrSelected.push($(this).val());
            });

            window.livewire.emit('pg:multiSelect-' + _this.tableName, {
                id: _this.dataField,
                values: arrSelected
            })

            $(element).selectpicker('refresh');
        });
    },
})
