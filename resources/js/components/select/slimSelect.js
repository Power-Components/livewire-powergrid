import { storeMultiSelect } from "./store";

export default (params) => ({
    init() {
        const element = this.$refs['select_picker_'+params.dataField+'_'+params.tableName];

        new window.TomSelect(element,{
            items: this.initialValues,
            ...this.framework,
            onChange: (value) => {
                storeMultiSelect(params, value)
            },
        });
    }
})
