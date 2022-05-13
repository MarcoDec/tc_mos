const types = ['file', 'password', 'select', 'text']

export function fieldValidator(field) {
    if (typeof field !== 'object' || field === null)
        return false
    if (typeof field.label !== 'string' || typeof field.name !== 'string')
        return false
    if (typeof field.type !== 'undefined') {
        if (typeof field.type !== 'string')
            return false
        if (!types.includes(field.type))
            return false
        if (field.type === 'select') {
            if (!Array.isArray(field.options))
                return false
            for (const option of field.options)
                if (
                    typeof option.text === 'undefined' || option.text === null
                    || typeof option.value === 'undefined' || option.value === null
                )
                    return false
        }
    }
    return true
}

export function generateField() {
    return {required: true, type: Object, validator: fieldValidator}
}

const variants = ['danger', 'dark', 'info', 'light', 'none', 'primary', 'secondary', 'success', 'warning']

function variantValidator(value) {
    return variants.includes(value)
}

export function generateVariant(defaultValue) {
    return {default: defaultValue, type: String, validator: variantValidator}
}
