import {generateField} from '../../../props'
import {h} from 'vue'

function AppTextArea(props, context) {
    return h('textarea', {
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

AppTextArea.emits = ['update:modelValue']
AppTextArea.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppTextArea
