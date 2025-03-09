export default (params) => ({
    rowId: params.rowId,
    rules: params.rules,
    defaultClasses: params.defaultClasses,
    attributes: [],
    theme: [],
    init() {
        if (this.rules) {
            Object.values(this.rules).forEach((rule) => {
                if ((rule.applyLoop || rule.apply) && rule?.attributes) {
                    this.attributes.push(rule.attributes)
                }
            })
        }
    },

    getAttributes() {
        let attributes = {
            class: this.defaultClasses
        };

        this.attributes.forEach(attribute => {
            Object.keys(attribute).forEach(key => {
                attributes[key] += attributes[key] ? ` ${attribute[key]}` : attribute[key]
            });
        });

        return attributes;
    }
});
