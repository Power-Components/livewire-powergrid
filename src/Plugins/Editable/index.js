if (!window.pgEditableRegistered) {
    const register = () => {
        window.Alpine.data('pgEditable', () => ({
            theme: null,
            id: null,
            dataField: null,
            content: null,
            fallback: null,
            inputClass: null,
            saveOnMouseOut: false,
            oldContent: null,
            editable: false,
            hash: null,
            hashError: true,
            showEditable: false,

            notEditing() {
                return !this.showEditable;
            },

            startEdit() {
                this.editable = true;
            },

            clickableId() {
                return 'clickable-' + this.dataField + '-' + this.id;
            },

            editableId() {
                return 'editable-' + this.dataField + '-' + this.id;
            },

            singleLineClass() {
                return 'pg-single-line ' + this.inputClass;
            },

            init() {
                const raw = this.$el.dataset.pgParams;
                const params = raw ? JSON.parse(raw) : {};

                this.theme = params.theme;
                this.id = params.id ?? null;
                this.dataField = params.dataField ?? null;
                this.content = params.content;
                this.fallback = params.fallback;
                this.inputClass = params.inputClass;
                this.saveOnMouseOut = params.saveOnMouseOut;

                if (this.content.length === 0 && this.fallback) {
                    this.content = this.htmlSpecialChars(this.fallback);
                }

                this.hash = this.dataField + '-' + this.id;

                window.addEventListener('toggle-' + this.hash, () => {
                    this.observe(
                        () => document.getElementById('clickable-' + this.hash),
                        (clickableElement) => {
                            clickableElement.click();
                            this.observe(
                                () => document.getElementById('editable-' + this.hash),
                                (editableElement) => {
                                    setTimeout(() => this.setFocusToEnd(editableElement), 100);
                                }
                            );
                        }
                    );
                });

                this.$watch('editable', (value) => {
                    if (value) {
                        let showEditable = false;
                        this.showEditable = false;
                        this.content = this.htmlSpecialChars(this.content);
                        this.oldContent = this.content;
                        this.hashError = this.store().notContains(this.hash);

                        this.observe(
                            () => document.getElementById('editable-' + this.hash),
                            (editableElement) => {
                                setTimeout(() => {
                                    this.setFocusToEnd(editableElement)
                                    if (this.store().getTextContent(this.hash)) {
                                        editableElement.textContent = this.store().getTextContent(this.hash);
                                    }
                                }, 30);
                            }
                        );

                        if (this.hashError) {
                            const pendingHash = this.store().get(this.hash);
                            const clickableElement = document.getElementById('clickable-' + pendingHash);
                            if (clickableElement) {
                                clickableElement.click();
                            }
                        }

                        showEditable = true;

                        this.$nextTick(() => setTimeout(() => {
                            this.showEditable = showEditable;
                            this.focus();
                        }, 150));
                    }
                });

                this.content = this.htmlSpecialChars(this.content);
            },

            store() {
                return window.editOnClickValidation;
            },

            save() {
                this.store().clear();
                this.store().set(this.hash, this.$el.textContent);

                this.observe(
                    () => document.getElementById('clickable-' + this.hash),
                    (clickableElement) => {
                        clickableElement.textContent = this.$el.textContent;
                    }
                );

                window.addEventListener('pg:editable-close-' + this.id, () => {
                    this.store().clear();
                    this.editable = false;
                    this.showEditable = false;
                });

                if (!this.store().has(this.hash)) {
                    this.store().set(this.hash, this.$el.textContent);
                }

                this.$wire.dispatch('pg:editable-' + this.$wire.tableName, {
                    field: this.dataField,
                    id: this.id,
                    value: this.$el.textContent
                });

                this.oldContent = this.store().getTextContent(this.hash);

                this.$nextTick(() => {
                    this.focus();
                    this.$el.setAttribute('value', '');
                });

                this.content = this.htmlSpecialChars(this.$el.textContent);
            },

            focus() {
                this.setFocusToEnd(this.$el);
            },

            cancel() {
                this.store().clear();
                this.$refs.editable.textContent = this.oldContent;
                this.content = this.oldContent;
                this.editable = false;
                this.showEditable = false;

                if (this.$refs.error) {
                    this.$refs.error.innerHTML = '';
                }
            },

            htmlSpecialChars(string) {
                const el = document.createElement('div');
                el.innerHTML = string;
                return el.textContent;
            },

            observe(elementFinder, action) {
                const observer = new MutationObserver((mutationsList, observer) => {
                    for (let mutation of mutationsList) {
                        if (mutation.type === 'childList') {
                            const element = elementFinder();
                            if (element) {
                                action(element);
                                observer.disconnect();
                                break;
                            }
                        }
                    }
                });

                observer.observe(document.body, { childList: true, subtree: true });
            },

            setFocusToEnd(element) {
                if (!element) return;
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(element);
                range.collapse(false);

                selection.removeAllRanges();
                selection.addRange(range);
                element.focus();
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
    window.pgEditableRegistered = true;
}
