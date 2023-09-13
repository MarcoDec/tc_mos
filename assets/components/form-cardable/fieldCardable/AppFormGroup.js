import {generateField, generateLabelCols} from '../../props'
import {h, resolveComponent} from 'vue'
import AppLabel from './AppLabel'

function AppFormGroup(props, context) {
    const id = `${props.form}-${props.field.name}`
    const attrs = {
        disabled: props.disabled,
        field: props.field,
        form: props.form,
        id,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => {
            context.emit('update:modelValue', value)
        },
        onSearchChange: value => {
            context.emit('searchChange', value)
        }
    }
    const children = []
    if (props.violation) {
        attrs['class'] = 'is-invalid'
        children.push(h('div', {class: 'invalid-feedback'}, props.violation.message))
    } else
        children.push(h(resolveComponent('AppInputGuesser'), attrs))
    return h('div', {class: 'row mb-3'}, [
        h(AppLabel, {cols: props.labelCols, field: props.field, for: id}),
        h('div', {class: 'col'}, children)
    ])
}

AppFormGroup.emits = ['update:modelValue', 'searchChange']

AppFormGroup.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    labelCols: generateLabelCols(),
    modelValue: {},
    violation: {default: null, type: Object}
}

export default AppFormGroup
