import { storeMultiSelect } from "./store";

export default (params) => ({
  initialValues: params.initialValues,
  framework: params.framework,
  init() {
    const element =
        this.$refs["select_picker_" + params.dataField + "_" + params.tableName];

    const frameworkCopy = this.deepCopy(this.framework);

    new window.SlimSelect({
      select: element,
      ...frameworkCopy,
      events: {
        afterChange: (value) => {
          let newValue = value.map(item => item.value);
          storeMultiSelect(params, newValue);
        },
      },
    });
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

