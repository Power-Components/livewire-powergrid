import { storeMultiSelect } from "./store";

export default () => ({
    initialValues: null,
    framework: null,

    init() {
        const params = JSON.parse(this.$el.dataset.pgParams || '{}');

        this.initialValues = params.initialValues;
        this.framework = params.framework;

        const element = this.$refs["select_picker_" + params.dataField + "_" + params.tableName];
        const frameworkCopy = this.deepCopy(this.framework);

        new window.SlimSelect({
            select: element,
            ...frameworkCopy,
            events: {
                afterChange: (value) => {
                    if (params.deferred) {
                        return;
                    }
                    let newValue = value.map(item => item.value);
                    storeMultiSelect(params, newValue);
                },
            },
        });

        // Apply filters from appliedFilters if they exist
        if (params.appliedFilters && params.dataField) {
            this.applyFilterForDataField(params.appliedFilters, params.dataField, element);
        }

        window.addEventListener('pg:clear_multi_select::' + params.tableName + ':' + params.dataField, () => {
            element.slim.setSelected([], false);
        });

        window.addEventListener('pg:clear_all_multi_select::' + params.tableName, () => {
            element.slim.setSelected([], false);
        });

        window.addEventListener('pg:restore_multi_select::' + params.tableName, () => {
            this.$wire.get('filters.multi_select.' + params.dataField).then((values) => {
                element.slim.setSelected(Array.isArray(values) ? values : [], false);
            });
        });
    },

    applyFilterForDataField(appliedFilters, dataField, element) {
        const pathParts = dataField.split('.');
        let current = appliedFilters;

        // Traverse the nested object structure
        for (const part of pathParts) {
            if (current && typeof current === 'object' && current.hasOwnProperty(part)) {
                current = current[part];
            } else {
                return; // Path doesn't exist in appliedFilters
            }
        }

        if (Array.isArray(current) && current.length > 0) {
            element.slim.setSelected(current, false);
        }
    },

    deepCopy(obj) {
        if (obj === null || typeof obj !== 'object') {
            return obj;
        }

        if (Array.isArray(obj)) {
            return obj.map(this.deepCopy);
        }

        const copy = {};
        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                copy[key] = this.deepCopy(obj[key]);
            }
        }

        return copy;
    }
});
