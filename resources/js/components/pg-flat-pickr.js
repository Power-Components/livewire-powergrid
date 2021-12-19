export default (params) => ({
    dataField: params.dataField ?? null,
    tableName: params.tableName ?? null,
    filterKey: params.filterKey ?? null,
    label: params.label ?? null,
    locale: params.locale ?? 'en',
    onlyFuture: params.onlyFuture ?? false,
    noWeekEnds: params.noWeekEnds ?? false,
    customConfig: params.customConfig ?? null,
    init() {
        const _this = this;
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

        options.onClose = function (selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                _this.filter(selectedDates)
            }
        }

        if (this.$refs.rangeInput) {
            flatpickr(this.$refs.rangeInput, options);
        }

    },
    filter(selectedDates) {
        console.log('pg:eventChangeDatePiker_' + this.tableName)
        window.livewire.emit('pg:eventChangeDatePiker_' + this.tableName, {
            selectedDates: selectedDates,
            field: this.dataField,
            values: this.filterKey,
            label: this.label
        });
    }
})
