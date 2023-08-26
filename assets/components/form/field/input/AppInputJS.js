import {generateField} from '../../../props'
import {h} from 'vue'

function AppInputJS(props, context) {
    return h('input', {
        autocomplete: 'off',
        class: 'form-control form-control-sm',
        disabled: props.disabled,
        form: props.form,
        id: props.id,
        multiple: props.field.multiple,
        name: props.field.name,
        onInput: e => context.emit('update:modelValue', e.target.value),
        placeholder: props.field.label,
        type: props.field.type ?? 'text',
        value: props.modelValue
    })
}

AppInputJS.emits = ['update:modelValue']
AppInputJS.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppInputJS
