export function generateLabelCols() {
    return {default: 'col-md-3 col-xs-12', type: String}
}

const variants = ['danger', 'dark', 'info', 'light', 'none', 'primary', 'secondary', 'success', 'warning']

function variantValidator(value) {
    return variants.includes(value)
}

export function generateVariant(defaultValue) {
    return {default: defaultValue, type: String, validator: variantValidator}
}
