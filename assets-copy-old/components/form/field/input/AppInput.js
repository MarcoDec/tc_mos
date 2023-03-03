import {generateField} from '../../../props'
import {h} from 'vue'

function AppInput(props, context) {
    return h('input', {
        autocomplete: 'off',
        class: 'form-control form-control-sm',
        disabled: props.disabled,
        form: props.form,
        id: props.id,
        name: props.field.name,
        onInput: e => context.emit('update:modelValue', e.target.value),
        placeholder: props.field.label,
        type: props.field.type ?? 'text',
        value: props.modelValue
    })
}

AppInput.emits = ['update:modelValue']
AppInput.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: false, type: String},
    id: {required: false, type: String},
    modelValue: {}
}

export default AppInput
