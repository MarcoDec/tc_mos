import {generateField, generateLabelCols} from '../../props'
import {h, resolveComponent} from 'vue'
import AppLabelJS from './AppLabelJS'

function AppFormGroupJS(props, context) {
    const id = `${props.form}-${props.field.name}`
    const attrs = {
        disabled: props.disabled,
        field: props.field,
        form: props.form,
        id,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => context.emit('update:modelValue', value)
    }
    const children = []
    if (props.violation) {
        attrs.class = 'is-invalid'
        children.push(h(resolveComponent('AppInputGuesserJS'), attrs))
        children.push(h('div', {class: 'invalid-feedback'}, props.violation.message))
    } else {
        children.push(h(resolveComponent('AppInputGuesserJS'), attrs))
    }
    return h('div', {class: 'row mb-1'}, [
        h(AppLabelJS, {cols: props.labelCols, field: props.field, for: id}),
        h('div', {class: 'col'}, children)
    ])
}

AppFormGroupJS.emits = ['update:modelValue']
AppFormGroupJS.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    labelCols: generateLabelCols(),
    modelValue: {},
    violation: {default: null, type: Object}
}

export default AppFormGroupJS
