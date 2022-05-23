import {h, resolveComponent} from 'vue'

function AppTableFormField(props, context) {
    function input(inputAttrs) {
        return typeof context.slots['default'] === 'function'
            ? context.slots['default'](inputAttrs)
            : h(resolveComponent('AppInputGuesser'), inputAttrs)
    }

    const attrs = {
        field: props.field,
        form: props.form,
        id: `${props.id}-input`,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => context.emit('update:modelValue', value)
    }
    const children = []
    if (props.violation) {
        attrs['class'] = 'is-invalid'
        children.push(input(attrs))
        children.push(h('div', {class: 'invalid-feedback'}, props.violation.message))
    } else
        children.push(input(attrs))
    return h('td', {id: props.id}, children)
}

AppTableFormField.emits = ['update:modelValue']
AppTableFormField.props = {
    field: {required: true, type: Object},
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {},
    violation: {default: null, type: Object}
}

export default AppTableFormField
