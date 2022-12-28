import { storeMultiSelect } from "./store";

export default (params) => ({
    init() {
        const element = this.$refs['select_picker_'+params.dataField+'_'+params.tableName];

        new window.TomSelect(element,{
            items: params.initialValues,
            ...params.framework,
            onChange: (value) => {
                storeMultiSelect(params, value)
            },
            valueField: params.optionId,
            labelField: params.optionLabel,
            searchField: params.optionLabel,
            load: async function (query, callback) {

                function makeRequest (asyncData, query) {
                    const { method, credentials, url, parameters } = asyncData

                    const request = new Request(url, {
                        method,
                        body: method === 'POST' ? JSON.stringify({
                            search: query,
                            ...parameters
                        }) : undefined,
                        credentials
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
                    return `<div class="py-2 d-flex">
							<div>
								<div class="mb-1">
									<span class="h4">
										${ escape(item.name) }
									</span>
								</div>
							</div>
						</div>`;
                },
                item: function(item, escape) {
                    return `<div class="py-2 d-flex">
							<div>
								<div class="mb-1">
									<span class="h4">
										${ escape(item.name) }
									</span>
								</div>
							</div>
						</div>`;
                }
            },
        });
    },
})
