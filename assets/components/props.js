import Field from '../fields/Field'
import Fields from '../fields/Fields'
import TableField from '../fields/TableField'
import TableFields from '../fields/TableFields'

export function generateField() {
    return {
        required: true,
        type: Object,
        validator: field => {
            if (field instanceof Field)
                return true
            console.error('field must be an instance of Field')
            return false
        }
    }
}

export function generateFields() {
    return {
        required: true,
        type: Object,
        validator(fields) {
            if (fields instanceof Fields)
                return true
            console.error('fields must be an instance of Fields')
            return false
        }
    }
}

export function generateLabelCols() {
    return {default: 'col-md-3 col-xs-12', type: String}
}

export function generateTableField() {
    return {
        required: true,
        type: Object,
        validator: field => {
            if (field instanceof TableField)
                return true
            console.error('field must be an instance of TableField')
            return false
        }
    }
}

export function generateTableFields() {
    return {
        required: true,
        type: Object,
        validator(fields) {
            if (fields instanceof TableFields)
                return true
            console.error('fields must be an instance of TableFields')
            return false
        }
    }
}

const variants = ['danger', 'dark', 'info', 'light', 'none', 'primary', 'secondary', 'success', 'warning']

export function generateVariant(defaultValue) {
    return {default: defaultValue, type: String, validator: value => variants.includes(value)}
}
