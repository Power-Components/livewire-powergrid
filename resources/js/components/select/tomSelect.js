window.pgAlpine.data('pgTomSelect', () => ({
    init() {
        const params = JSON.parse(this.$el.dataset.pgParams || '{}')

        const TomSelect = window.TomSelect

        if (typeof TomSelect === 'undefined') {
            console.error('[PowerGrid] tom-select is not available. Install it (`npm i tom-select`) and expose it globally, e.g. `import TomSelect from "tom-select"; window.TomSelect = TomSelect;`')

            return
        }

        const element = this.$refs[`select_picker_${params.dataField}_${params.tableName}`]

        const defaultParams = {
            items: params.initialValues,
            ...params.framework,
            onChange: (value) => {
                if (params.deferred) {
                    return
                }
                window.pgStoreMultiSelect(params, value)
            },
            onInitialize: () => {
                if (params.appliedFilters && params.dataField) {
                    this.applyFilterForDataField(params.appliedFilters, params.dataField, element)
                }

                window.addEventListener(`pg:clear_multi_select::${params.tableName}:${params.dataField}`, () => {
                    if (element) {
                        element.tomselect.clear(true)
                    }
                })

                window.addEventListener(`pg:clear_all_multi_select::${params.tableName}`, () => {
                    if (element) {
                        element.tomselect.clear(true)
                    }
                })

                window.addEventListener(`pg:restore_multi_select::${params.tableName}`, () => {
                    if (!element) {
                        return
                    }

                    this.$wire.get('filters.multi_select.' + params.dataField).then((values) => {
                        element.tomselect.setValue(Array.isArray(values) ? values : [])
                    })
                })
            },
        }

        const asyncConfig = {
            valueField: params.optionValue,
            labelField: params.optionLabel,
            searchField: params.optionLabel,
            load: async function (query, callback) {
                fetch(makeAsyncRequest(params.asyncData, query))
                    .then(response => response.json())
                    .then(json => {
                        callback(json)
                    }).catch(() => {
                    callback()
                })
            },
            render: {
                option: function (item, escape) {
                    return `<div class="py-2 mb-1"><span>${escape(item[params.optionLabel])}</span></div>`
                },
                item: function (item, escape) {
                    return `<div class="py-2 mb-1"><span>${escape(item[params.optionLabel])}</span></div>`
                }
            },
        }

        let parameters = defaultParams

        if (Object.prototype.hasOwnProperty.call(params, 'asyncData')) {
            parameters = Object.assign(defaultParams, asyncConfig)
        }

        new TomSelect(element, parameters)
    },

    applyFilterForDataField(appliedFilters, dataField, element) {
        const pathParts = dataField.split('.')
        let current = appliedFilters

        for (const part of pathParts) {
            if (current && typeof current === 'object' && Object.prototype.hasOwnProperty.call(current, part)) {
                current = current[part]
            } else {
                return
            }
        }

        if (Array.isArray(current) && current.length > 0) {
            element.tomselect.addItems(current)
        }
    }
}))

function makeAsyncRequest(asyncData, query) {
    const { method, url } = asyncData
    const extra = asyncData.parameters && typeof asyncData.parameters === 'object'
        ? asyncData.parameters
        : {}
    const headers = asyncData.headers && typeof asyncData.headers === 'object'
        ? asyncData.headers
        : {}

    const isPost = String(method).toLowerCase() === 'post'
    let requestUrl = url

    if (!isPost) {
        const parsed = new URL(url, window.location.origin)
        parsed.searchParams.set('search', query)
        Object.entries(extra).forEach(([key, value]) => {
            if (value !== undefined && value !== null) {
                parsed.searchParams.set(key, String(value))
            }
        })
        requestUrl = parsed.toString()
    }

    const request = new Request(requestUrl, {
        method,
        body: isPost ? JSON.stringify({
            search: query,
            ...extra,
        }) : undefined,
    })

    request.headers.set('Content-Type', 'application/json')
    request.headers.set('Accept', 'application/json')
    request.headers.set('X-Requested-With', 'XMLHttpRequest')

    Object.entries(headers).forEach(([key, value]) => {
        request.headers.set(key, String(value))
    })

    const csrfToken = document.head.querySelector('[name="csrf-token"]')?.getAttribute('content')

    if (csrfToken) {
        request.headers.set('X-CSRF-TOKEN', csrfToken)
    }

    return request
}
