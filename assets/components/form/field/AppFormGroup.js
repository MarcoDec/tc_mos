import AppInputGuesser from './input/AppInputGuesser'
import AppLabel from './AppLabel'
import {generateField} from '../../validators'
import {h} from 'vue'

function AppFormGroup(props, context) {
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
        attrs['class'] = 'is-invalid'
        children.push(h(AppInputGuesser, attrs))
        children.push(h('div', {class: 'invalid-feedback'}, props.violation.message))
    } else
        children.push(h(AppInputGuesser, attrs))
    return h('div', {class: 'row mb-3'}, [
        h(AppLabel, {field: props.field, for: id}),
        h('div', {class: 'col'}, children)
    ])
}

AppFormGroup.emits = ['update:modelValue']
AppFormGroup.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    modelValue: {},
    violation: {default: null, type: Object}
}

export default AppFormGroup
