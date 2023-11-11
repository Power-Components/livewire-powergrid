import { storeMultiSelect } from "./store";

export default (params) => ({
  init() {
    console.log(params);
    const element =
      this.$refs["select_picker_" + params.dataField + "_" + params.tableName];

    new window.SlimSelect({
      select: element,
      data: this.initialValues,
      ...this.framework,
      events: {
        afterChange: (value) => {
          storeMultiSelect(params, value);
        },
      },
    });
  },
});
