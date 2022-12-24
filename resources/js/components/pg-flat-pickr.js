export default (params) => ({
    dataField: params.dataField,
    tableName: params.tableName,
    filterKey: params.filterKey,
    label: params.label ?? null,
    locale: params.locale ?? 'en',
    onlyFuture: params.onlyFuture ?? false,
    noWeekEnds: params.noWeekEnds ?? false,
    customConfig: params.customConfig ?? null,
    init() {
        const lang = this.locale.locale;

        if (typeof lang !== 'undefined') {
            this.locale.locale = require("flatpickr/dist/l10n/"+lang+".js").default[lang];
        }

        const options = {
            mode: 'range',
            defaultHour: 0,
            ...this.locale,
            ...this.customConfig
        }

        if (this.onlyFuture) {
            options.minDate = 'today';
        }

        if (this.noWeekEnds) {
            options.disable = [
                function (date) {
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ];
        }

        options.onClose = (selectedDates, dateStr, instance) => {
            if (selectedDates.length === 2) {
                window.livewire.emit('pg:datePicker-' + this.tableName, {
                    selectedDates: selectedDates,
                    field: this.dataField,
                    values: this.filterKey,
                    label: this.label,
                    dateStr: dateStr,
                    enableTime: options.enableTime === undefined ? false : options.enableTime
                });
            }
        }

        if(this.$refs.rangeInput) {
            flatpickr(this.$refs.rangeInput, options);
        }
    },
})
