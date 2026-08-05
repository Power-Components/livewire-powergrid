if (!window.pgToggleableRegistered) {
    const register = () => {
        window.Alpine.data('pgToggleable', () => ({
            field: null,
            tableName: null,
            enabled: false,
            id: null,
            trueValue: null,
            falseValue: null,
            toggle: null,

            init() {
                const raw = this.$el.dataset.pgParams;
                const params = raw ? JSON.parse(raw) : {};

                this.field = params.field ?? null;
                this.tableName = params.tableName ?? null;
                this.enabled = params.enabled ?? false;
                this.id = params.id;
                this.trueValue = params.trueValue;
                this.falseValue = params.falseValue;
                this.toggle = params.toggle;
            },

            ariaChecked() {
                return this.toggle ? 'true' : 'false';
            },

            onClass() {
                return { 'pg-toggleable-on': this.toggle };
            },

            save() {
                this.toggle = (this.toggle === 0 ?  1 :  0);

                this.$wire.dispatch('pg:toggleable-' + this.tableName, {
                    field: this.field,
                    id: this.id,
                    value: this.toggle
                })
            }
        }));
    };

    if (window.Alpine) {
        register();
    } else {
        document.addEventListener('alpine:init', () => {
            register();
        });
    }
    window.pgToggleableRegistered = true;
}
