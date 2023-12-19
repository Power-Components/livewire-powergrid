import { storeMultiSelect } from "./store";
import TomSelect from "tom-select";

export default (params) => ({
    init() {
        const element = this.$refs[`select_picker_${params.dataField}_${params.tableName}`];

        const defaultParams = {
            items: params.initialValues,
            ...params.framework,
            onChange: (value) => {
                storeMultiSelect(params, value)
            },
            onInitialize: () => {
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
            },
        }

        const asyncConfig = {
            valueField: params.optionValue,
            labelField: params.optionLabel,
            searchField: params.optionLabel,
            load: async function (query, callback) {

                function makeRequest (asyncData, query) {
                    const { method, url } = asyncData

                    const request = new Request(url, {
                        method,
                        body: method === 'POST' ? JSON.stringify({
                            search: query,
                            ...parameters
                        }) : undefined,
                    })

                    request.headers.set('Content-Type', 'application/json')
                    request.headers.set('Accept', 'application/json')
                    request.headers.set('X-Requested-With', 'XMLHttpRequest')

                    const csrfToken = document.head.querySelector('[name="csrf-token"]')?.getAttribute('content')

                    if (csrfToken) {
                        request.headers.set('X-CSRF-TOKEN', csrfToken)
                    }

                    return request

                }

                fetch(makeRequest(params.asyncData, query))
                    .then(response => response.json())
                    .then(json => {
                        callback(json);
                    }).catch(()=>{
                    callback();
                });

            },
            render: {
                option: function(item, escape) {
                    return `<div class="py-2 mb-1"><span>${ escape(item[params.optionLabel]) }</span></div>`;
                },
                item: function(item, escape) {
                    return `<div class="py-2 mb-1"><span>${ escape(item[params.optionLabel]) }</span></div>`;
                }
            },
        }

        let parameters = defaultParams

        if (params.hasOwnProperty('asyncData')) {
            parameters = Object.assign(defaultParams, asyncConfig)
        }

        new TomSelect(element, parameters);

    },
})
