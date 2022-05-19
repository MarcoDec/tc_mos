import {h} from 'vue'

function AppTableItemField(props, context) {
    const value = props.item[props.field.name]
    return h(
        'td',
        null,
        typeof context.slots['default'] === 'function'
            ? context.slots['default']({field: props.field, item: props.item, value})
            : value
    )
}

AppTableItemField.props = {field: {required: true, type: Object}, item: {required: true, type: Object}}

export default AppTableItemField
