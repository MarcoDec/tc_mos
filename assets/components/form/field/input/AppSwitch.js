import {generateField} from '../../../validators'
import {h} from 'vue'

function AppSwitch(props, context) {
    return h('div', {class: 'form-check form-switch'}, [
        h('input', {
            checked: props.modelValue,
            class: 'form-check-input',
            disabled: props.disabled,
            form: props.form,
            id: props.id,
            name: props.field.name,
            onInput: e => context.emit('update:modelValue', e.target.checked),
            type: 'checkbox'
        })
    ])
}

AppSwitch.emits = ['update:modelValue']
AppSwitch.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppSwitch
