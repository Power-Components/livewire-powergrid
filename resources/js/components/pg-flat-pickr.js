export default (params) => ({
    dataField: params.dataField,
    tableName: params.tableName,
    filterKey: params.filterKey,
    label: params.label ?? null,
    locale: params.locale ?? 'en',
    onlyFuture: params.onlyFuture ?? false,
    noWeekEnds: params.noWeekEnds ?? false,
    customConfig: params.customConfig ?? null,
    element: null,
    lastRequest: null,
    init() {
        const lang = this.locale.locale;

        if (typeof lang !== 'undefined') {
            this.locale.locale = require("flatpickr/dist/l10n/"+lang+".js").default[lang];
        }

        window.addEventListener(`pg:clear_flatpickr::${this.tableName}:${this.dataField}`, () => {
            if (this.$refs.rangeInput && this.element) {
                this.element.clear()
                this.lastRequest = null;
            }
        })

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
            const key = `${this.tableName}::${this.dataField}::${selectedDates}`
            const encrypted = Buffer.from(key).toString('base64')

            if (selectedDates.length > 0 && encrypted !== this.lastRequest) {
                window.Livewire.emit('pg:datePicker-' + this.tableName, {
                    selectedDates: selectedDates,
                    field: this.dataField,
                    values: this.filterKey,
                    label: this.label,
                    dateStr: dateStr,
                    enableTime: options.enableTime === undefined ? false : options.enableTime
                });

                this.lastRequest = encrypted;
            }
        }

        if(this.$refs.rangeInput) {
            this.element = flatpickr(this.$refs.rangeInput, options);
        }
    },
})
