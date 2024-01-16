const types = ['boolean', 'color', 'file', 'multiselect', 'multiselect-fetch', 'number', 'password', 'select', 'text', 'textarea', 'time', 'measure', 'address', 'date', 'datetime-local', 'rating', 'measureSelect', 'grpbutton', 'link']

export function fieldValidator(field) {
    if (typeof field !== 'object' || field === null || Array.isArray(field)) {
        console.error('field must be not null object')
        return false
    }
    if (typeof field.label !== 'string' || typeof field.name !== 'string') {
        console.error('field.label and field.name must be defined and a string', field)
        return false
    }
    if (typeof field.type !== 'undefined') {
        if (typeof field.type !== 'string') {
            console.error('field.type must be string')
            return false
        }
        if (!types.includes(field.type)) {
            console.error(`field.type must be on of [${types.join(', ')}]`, field.type)
            return false
        }
        if (field.type === 'select') {
            if (typeof field.options !== 'object' || field.options === null) {
                console.error('field.options must be defined and not null')
                return false
            }
            if (typeof field.options.base === 'undefined'){
                if (!Array.isArray(field.options.options)) {
                    console.error('field.options.options must be defined and an array', field.options)
                    return false
                }
                for (const option of field.options.options)
                    if (typeof option.text === 'undefined' || option.text === null || typeof option.value === 'undefined') {
                        console.error('field.options', 'field.text and field.value must be defined', field, option)
                        return false
                    }
            } else {
                //console.log('field.options.base', field.options.base)
            }
        }
    }
    return true
}

export function generateField(validator = fieldValidator) {
    return {required: true, type: Object, validator}
}

export function generateFields(validator = fieldValidator) {
    return {
        required: true,
        type: Array,
        validator(value) {
            if (value.length === 0) {
                console.error('at least one field must be defined')
                return false
            }
            for (const field of value)
                if (!validator(field))
                    return false
            return true
        }
    }
}

export function generateLabelCols() {
    return {default: 'col-md-3 col-xs-12', type: String}
}

function tableFieldValidator(field) {
    if (!fieldValidator(field))
        return false
    if (typeof field.sort !== 'boolean') {
        console.error('field.sort must be defined and a boolean')
        return false
    }
    if (typeof field.update !== 'boolean') {
        console.error('field.update must be defined and a boolean')
        return false
    }
    if (typeof field.sortName !== 'undefined' && (typeof field.sortName !== 'string' || field.sortName.length === 0)) {
        console.error('field.sortName must be a non empty string')
        return false
    }
    return true
}

export function generateTableField() {
    return generateField(tableFieldValidator)
}

export function generateTableFields() {
    return generateFields(tableFieldValidator)
}

const variants = ['danger', 'dark', 'info', 'light', 'none', 'primary', 'secondary', 'success', 'warning']

function variantValidator(value) {
    return variants.includes(value)
}

export function generateVariant(defaultValue) {
    return {default: defaultValue, type: String, validator: variantValidator}
}
