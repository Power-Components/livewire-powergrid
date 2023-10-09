export default (params) => ({
    dataField: params.dataField,
    tableName: params.tableName,
    label: params.label ?? null,
    locale: params.locale ?? 'en',
    onlyFuture: params.onlyFuture ?? false,
    noWeekEnds: params.noWeekEnds ?? false,
    customConfig: params.customConfig ?? null,
    type: params.type,
    element: null,
    selectedDates: null,
    init() {
        window.addEventListener(`pg:clear_flatpickr::${this.tableName}:${this.dataField}`, () => {
            if (this.$refs.rangeInput && this.element) {
                this.element.clear()
            }
        })

        window.addEventListener(`pg:clear_all_flatpickr::${this.tableName}`, () => {
            if (this.$refs.rangeInput && this.element) {
                this.element.clear()
            }
        })

        const lang = this.locale.locale;

        if (typeof lang !== 'undefined') {
            this.locale.locale = require("flatpickr/dist/l10n/"+lang+".js").default[lang];
        }

        const options = this.getOptions()

        if(this.$refs.rangeInput) {
            this.element = flatpickr(this.$refs.rangeInput, options);

            this.selectedDates = this.$wire.get(`filters.${this.type}.${this.dataField}.formatted`)

            this.element.setDate(this.selectedDates)
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

            if (selectedDates.length > 0 && (this.selectedDates !== dateStr)) {
                Livewire.dispatch('pg:datePicker-' + this.tableName, {
                    selectedDates: selectedDates,
                    dateStr,
                    timezone: this.customConfig.timezone ?? new Date().toString().match(/([-\+][0-9]+)\s/)[1],
                    type: this.type,
                    field: this.dataField,
                    label: this.label,
                });
            }
        }

        return options;
    }
})
