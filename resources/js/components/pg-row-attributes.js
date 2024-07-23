export default (params) => ({
    rowId: params.rowId,
    rules: [],
    defaultClasses: params.defaultClasses,
    attributes: [],
    theme: [],
    init() {
        this.rules = this.$wire.actionRulesForRows[this.rowId]

        Object.values(this.rules).forEach((rule) => {
            if (rule.applyLoop) {
                this.attributes.push(rule.attributes)
            }
        })
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
