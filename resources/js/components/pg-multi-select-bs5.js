export default (params) => ({
    dataField: params.dataField ?? null,
    init() {
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

            window.livewire.emit('pg:eventMultiSelect', {
                id: field,
                values: arrSelected
            })
            $(element).selectpicker('refresh');
        });
    },

})
