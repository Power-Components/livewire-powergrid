let onCopy = () => {}

function PgClipboard(Alpine) {
    Alpine.magic('pgClipboard', () => {
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

PgClipboard.configure = (config) => {
    if (config.hasOwnProperty('onCopy') && typeof config.onCopy === 'function') {
        onCopy = config.onCopy
    }

    return PgClipboard
}

export default PgClipboard;
