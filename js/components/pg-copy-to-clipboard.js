let onCopy = () => {}

function Clipboard(Alpine) {
    Alpine.magic('clipboard', () => {
        return function (target) {
            if (typeof target === 'function') {
                target = target()
            }

            if (typeof target === 'object') {
                target = JSON.stringify(target)
            }

            return window.navigator.clipboard.writeText(target)
                .then(onCopy)
        }
    })
}

Clipboard.configure = (config) => {
    if (config.hasOwnProperty('onCopy') && typeof config.onCopy === 'function') {
        onCopy = config.onCopy
    }

    return Clipboard
}

export default Clipboard;
