const types = ['boolean', 'color', 'file', 'number', 'password', 'select', 'text']

export function fieldValidator(field) {
    if (typeof field !== 'object' || field === null || Array.isArray(field)) {
        console.error('field must be not null object')
        return false
    }
    if (typeof field.label !== 'string' || typeof field.name !== 'string') {
        console.error('field.label and field.name must be defined and a string')
        return false
    }
    if (typeof field.type !== 'undefined') {
        if (typeof field.type !== 'string') {
            console.error('field.type must be string')
            return false
        }
        if (!types.includes(field.type)) {
            console.error(`field.type must be on of [${types.join(', ')}]`)
            return false
        }
        if (field.type === 'select') {
            if (typeof field.options !== 'object' || field.options === null) {
                console.error('field.options must be defined and not null')
                return false
            }
            if (!Array.isArray(field.options.options)) {
                console.error('field.options.options must be defined and an array')
                return false
            }
            for (const option of field.options.options)
                if (
                    typeof option.text === 'undefined' || option.text === null
                    || typeof option.value === 'undefined' || option.value === null
                ) {
                    console.error('field.options', 'field.text and field.value must be defined and not null')
                    return false
                }
        }
    }
    return true
}

export function generateField() {
    return {required: true, type: Object, validator: fieldValidator}
}

export function generateFields() {
    return {
        required: true,
        type: Array,
        validator(value) {
            if (value.length === 0) {
                console.error('at least one field must be defined')
                return false
            }
            for (const field of value)
                if (!fieldValidator(field))
                    return false
            return true
        }
    }
}

export function generateTableField() {
    return generateField()
}

export function generateTableFields() {
    return generateFields()
}

const variants = ['danger', 'dark', 'info', 'light', 'none', 'primary', 'secondary', 'success', 'warning']

function variantValidator(value) {
    return variants.includes(value)
}

export function generateVariant(defaultValue) {
    return {default: defaultValue, type: String, validator: variantValidator}
}
