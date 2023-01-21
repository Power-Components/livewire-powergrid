export default (params) => ({
    dataField: params.dataField,
    tableName: params.tableName,
    filterKey: params.filterKey,
    label: params.label ?? null,
    locale: params.locale ?? 'en',
    onlyFuture: params.onlyFuture ?? false,
    noWeekEnds: params.noWeekEnds ?? false,
    customConfig: params.customConfig ?? null,
    input: null,
    init() {
        window.addEventListener('pg:datePicker-' + this.tableName +'-clear' , () => {
            this.input.clear()
        })

        const lang = this.locale.locale;

        if (typeof lang !== 'undefined') {
            this.locale.locale = require("flatpickr/dist/l10n/"+lang+".js").default[lang];
        }

        const options = this.getOptions()

        if(this.$refs.rangeInput) {
            this.input = flatpickr(this.$refs.rangeInput, options);
        }
    },
    getOptions() {
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

        const _this = this;
        options.onClose = function (selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                _this.$wire.emit('pg:datePicker-' + _this.tableName, {
                    selectedDates: selectedDates,
                    field: _this.dataField,
                    values: _this.filterKey,
                    label: _this.label,
                    dateStr: dateStr,
                    tableName: _this.tableName,
                    enableTime: options.enableTime === undefined ? false : options.enableTime
                });
            }
        }

        return options;
    }
})
