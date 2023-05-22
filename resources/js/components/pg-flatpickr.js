export default (params) => ({
    dataField: params.dataField,
    tableName: params.tableName,
    filterKey: params.filterKey,
    label: params.label ?? null,
    locale: params.locale ?? 'en',
    onlyFuture: params.onlyFuture ?? false,
    noWeekEnds: params.noWeekEnds ?? false,
    customConfig: params.customConfig ?? null,
    type: params.type,
    element: null,
    lastRequest: null,
    init() {
        window.addEventListener(`pg:clear_flatpickr::${this.tableName}:${this.dataField}`, () => {
            if (this.$refs.rangeInput && this.element) {
                this.element.clear()
                this.lastRequest = null;
            }
        })

        window.addEventListener(`pg:clear_all_flatpickr::${this.tableName}`, () => {
            if (this.$refs.rangeInput && this.element) {
                this.element.clear()
                this.lastRequest = null;
            }
        })

        const lang = this.locale.locale;

        if (typeof lang !== 'undefined') {
            this.locale.locale = require("flatpickr/dist/l10n/"+lang+".js").default[lang];
        }

        const options = this.getOptions()

        if(this.$refs.rangeInput) {
            this.element = flatpickr(this.$refs.rangeInput, options);
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

        options.onClose = (selectedDates, dateStr, instance) => {
            selectedDates = selectedDates.map((date) => this.element.formatDate(date, 'Y-m-d'));

            const key = `${this.tableName}::${this.dataField}::${selectedDates}`
            const encrypted = Buffer.from(key).toString('base64')

            if (selectedDates.length > 0 && encrypted !== this.lastRequest) {
                window.Livewire.emit('pg:datePicker-' + this.tableName, {
                    selectedDates: selectedDates,
                    field: this.dataField,
                    timezone: this.customConfig.timezone ?? new Date().toString().match(/([-\+][0-9]+)\s/)[1],
                    values: this.filterKey,
                    label: this.label,
                    dateStr: dateStr,
                    tableName: this.tableName,
                    type: this.type,
                    enableTime: this.customConfig.enableTime === undefined ? false : this.customConfig.enableTime
                });
            }
        }

        return options;
    }
})
