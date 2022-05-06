const variants = ['danger', 'dark', 'info', 'light', 'primary', 'secondary', 'success', 'warning']

function validator(value) {
    return variants.includes(value)
}

export function generateVariant(defaultValue) {
    return {default: defaultValue, type: String, validator}
}
