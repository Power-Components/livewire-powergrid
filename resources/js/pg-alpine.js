window.pgAlpine = window.pgAlpine || {
    data(name, factory) {
        const flag = 'pgRegistered_' + name

        if (window[flag]) {
            return
        }

        window[flag] = true

        const register = () => window.Alpine.data(name, factory)

        if (window.Alpine) {
            register()
        } else {
            document.addEventListener('alpine:init', register)
        }
    },

    store(name, definition) {
        const flag = 'pgRegisteredStore_' + name

        if (window[flag]) {
            return
        }

        window[flag] = true

        const register = () => {
            window.Alpine.store(name, definition)
            window[name] = window.Alpine.store(name)
        }

        if (window.Alpine) {
            register()
        } else {
            document.addEventListener('alpine:init', register)
        }
    },

    directive(name, callback) {
        const flag = 'pgRegisteredDirective_' + name

        if (window[flag]) {
            return
        }

        window[flag] = true

        const register = () => window.Alpine.directive(name, callback)

        if (window.Alpine) {
            register()
        } else {
            document.addEventListener('alpine:init', register)
        }
    },
}
