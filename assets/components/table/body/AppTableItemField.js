import {h, resolveComponent} from 'vue'

function AppTableItemField(props, context) {
    const value = props.item[props.field.name]
    return props.field.type === 'boolean'
        ? h(resolveComponent('AppTableFormField'), {
            disabled: true,
            field: props.field,
            form: 'none',
            id: props.id,
            machine: props.machine,
            modelValue: value
        })
        : h(
            'td',
            {id: props.id},
            typeof context.slots['default'] === 'function'
                ? context.slots['default']({field: props.field, item: props.item, value})
                : value
        )
}

AppTableItemField.props = {
    field: {required: true, type: Object},
    id: {required: true, type: String},
    item: {required: true, type: Object},
    machine: {required: true, type: Object}
}

export default AppTableItemField
