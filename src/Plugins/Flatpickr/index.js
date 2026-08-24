document.addEventListener('alpine:init', () => {
    Alpine.data('pgFlatpickr', () => ({
        dataField: null,
        tableName: null,
        label: null,
        locale: null,
        onlyFuture: false,
        noWeekEnds: false,
        customConfig: null,
        type: null,
        element: null,
        selectedDates: null,
        deferred: false,
        filtersProperty: 'filters',
        async init() {
            const raw = this.$el.dataset.pgParams;
            const params = raw ? JSON.parse(raw) : {};

            this.dataField = params.dataField;
            this.tableName = params.tableName;
            this.deferred = params.deferred ?? false;
            this.filtersProperty = params.filtersProperty ?? 'filters';
            this.label = params.label ?? null;
            this.locale = params.locale ?? {
                locale: 'default',
                dateFormat: 'Y-m-d H:i',
                time_24hr: false,
                enableTime: true
            };
            this.onlyFuture = params.onlyFuture ?? false;
            this.noWeekEnds = params.noWeekEnds ?? false;
            this.customConfig = params.customConfig ?? null;
            this.type = params.type;

            if(typeof flatpickr == "undefined") {
                console.error('%c%s',
                    'font-size: 1em; line-height: 1.5',
                    `
    Failed to mount filter: Filter::datetime('${this.dataField}') on table ['${this.tableName}']

    • Install flatpickr: npm install flatpickr

    • Add 'import flatpickr from "flatpickr"' in resources/js/app.js file
      `
                );
            }

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



            const options = this.getOptions()

            if(this.$refs.rangeInput && typeof flatpickr != "undefined") {
                this.element = flatpickr(this.$refs.rangeInput, options);

                this.selectedDates = this.$wire.get(`${this.filtersProperty}.${this.type}.${this.dataField}.formatted`)

                this.element.setDate(this.selectedDates)
            }
        },
        getOptions() {
            const options = {
                mode: 'range',
                defaultHour: 0,
                static: true,
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
                    // Panel modes defer to Apply: the draftFilters wire:model captures
                    // `formatted`; start/end are derived server-side on applyFilters().
                    if (this.deferred) {
                        return;
                    }
                    Livewire.dispatch('pg:datePicker-' + this.tableName, {
                        field: this.dataField,
                        selectedDates: selectedDates,
                        dateStr: dateStr,
                        label: this.label,
                        type: this.type,
                        timezone: this.customConfig.timezone ?? new Date().toString().match(/([-\+][0-9]+)\s/)[1],
                        dateFormat: this.locale.dateFormat ?? 'Y-m-d H:i'
                    });
                }
            }

            return options;
        }
    }))
})
