window.pgAlpine.data('pgSlimSelect', () => ({
    initialValues: null,
    framework: null,

    init() {
        const params = JSON.parse(this.$el.dataset.pgParams || '{}')

        if (typeof window.SlimSelect === 'undefined') {
            console.error('[PowerGrid] slim-select is not available. Install it (`npm i slim-select`) and expose it globally, e.g. `import SlimSelect from "slim-select"; window.SlimSelect = SlimSelect;`')

            return
        }

        this.initialValues = params.initialValues
        this.framework = params.framework

        const element = this.$refs["select_picker_" + params.dataField + "_" + params.tableName]
        const frameworkCopy = this.deepCopy(this.framework)

        new window.SlimSelect({
            select: element,
            ...frameworkCopy,
            events: {
                afterChange: (value) => {
                    if (params.deferred) {
                        return
                    }
                    let newValue = value.map(item => item.value)
                    window.pgStoreMultiSelect(params, newValue)
                },
            },
        })

        if (params.appliedFilters && params.dataField) {
            this.applyFilterForDataField(params.appliedFilters, params.dataField, element)
        }

        window.addEventListener('pg:clear_multi_select::' + params.tableName + ':' + params.dataField, () => {
            element.slim.setSelected([], false)
        })

        window.addEventListener('pg:clear_all_multi_select::' + params.tableName, () => {
            element.slim.setSelected([], false)
        })

        window.addEventListener('pg:restore_multi_select::' + params.tableName, () => {
            this.$wire.get('filters.' + params.dataField + '.value').then((values) => {
                element.slim.setSelected(Array.isArray(values) ? values : [], false)
            })
        })
    },

    applyFilterForDataField(appliedFilters, dataField, element) {
        if (Array.isArray(appliedFilters) && appliedFilters.length > 0) {
            element.slim.setSelected(appliedFilters, false)
        }
    },

    deepCopy(obj) {
        if (obj === null || typeof obj !== 'object') {
            return obj
        }

        if (Array.isArray(obj)) {
            return obj.map((item) => this.deepCopy(item))
        }

        const copy = {}
        for (const key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                copy[key] = this.deepCopy(obj[key])
            }
        }

        return copy
    }
}))
