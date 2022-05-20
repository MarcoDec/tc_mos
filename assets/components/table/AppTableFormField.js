import {h, resolveComponent} from 'vue'

function AppTableFormField(props, context) {
    const attrs = {
        field: props.field,
        form: props.form,
        id: `${props.id}-input`,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => context.emit('update:modelValue', value)
    }
    return h(
        'td',
        {id: props.id},
        typeof context.slots['default'] === 'function'
            ? context.slots['default'](attrs)
            : h(resolveComponent('AppInputGuesser'), attrs)
    )
}

AppTableFormField.emits = ['update:modelValue']
AppTableFormField.props = {
    field: {required: true, type: Object},
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppTableFormField
