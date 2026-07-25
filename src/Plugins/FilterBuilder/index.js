if (!window.pgFilterBuilderRegistered) {
    const register = () => {
        window.Alpine.data('pgFilterBuilder', (params) => ({
            tableName: params.tableName,
            modalName: 'pg-filter-builder-' + params.tableName,
            columns: params.columns || [],
            operatorLabels: params.operatorLabels || {},
            valueless: params.valueless || [],
            range: params.range || [],
            defaultBoolean: params.match === 'or' ? 'or' : 'and',
            rows: [],

            init() {
                const applied = params.applied || {};

                const rows = Array.isArray(applied.rows) ? applied.rows : [];

                this.rows = rows.map((row) => ({
                    column: row.column,
                    operator: row.operator,
                    value: row.value ?? '',
                    value2: row.value2 ?? '',
                    boolean: row.boolean === 'or' ? 'or' : 'and',
                }));

                if (this.rows.length === 0) {
                    this.addRow();
                }
            },

            columnMeta(field) {
                return this.columns.find((column) => column.field === field);
            },

            typeFor(field) {
                return this.columnMeta(field)?.type;
            },

            operatorsFor(field) {
                return this.columnMeta(field)?.operators || [];
            },

            optionsFor(field) {
                return this.columnMeta(field)?.options || [];
            },

            needsNoValue(operator) {
                return this.valueless.includes(operator);
            },

            needsRange(operator) {
                return this.range.includes(operator);
            },

            inputType(field) {
                const type = this.typeFor(field);

                if (type === 'number') return 'number';
                if (type === 'date') return 'date';
                if (type === 'datetime') return 'datetime-local';

                return 'text';
            },

            addRow() {
                if (this.columns.length === 0) {
                    return;
                }

                const column = this.columns[0].field;

                this.rows.push({
                    column: column,
                    operator: (this.operatorsFor(column)[0] || ''),
                    value: '',
                    value2: '',
                    boolean: this.defaultBoolean,
                });
            },

            removeRow(index) {
                this.rows.splice(index, 1);

                if (this.rows.length === 0) {
                    this.addRow();
                }
            },

            onColumnChange(row) {
                row.operator = this.operatorsFor(row.column)[0] || '';
                row.value = '';
                row.value2 = '';
            },

            apply() {
                this.$wire.applyFilterBuilder({
                    match: this.defaultBoolean,
                    rows: this.rows,
                });

                this.close();
            },

            reset() {
                this.rows = [];
                this.addRow();
                this.$wire.resetFilterBuilder();
                this.close();
            },

            close() {
                if (window.Flux && typeof window.Flux.modal === 'function') {
                    try {
                        window.Flux.modal(this.modalName).close();
                    } catch (error) {
                        //
                    }
                }
            },
        }));
    };

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', () => {
            register();
        });
    }

    window.pgFilterBuilderRegistered = true;
}
