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
                window.livewire.emit('pg:datePicker-' + _this.tableName, {
                    selectedDates: selectedDates,
                    field: _this.dataField,
                    values: _this.filterKey,
                    label: _this.label
                });
            }
        }

        if(this.$refs.rangeInput) {
            flatpickr(this.$refs.rangeInput, options);
        }
    },
})
