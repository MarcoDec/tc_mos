import AppOption from './AppOption'
import {generateField} from '../../../../validators'
import {h} from 'vue'

function AppSelect(props, context) {
    return h(
        'select',
        {
            class: 'form-select form-select-sm',
            disabled: props.disabled,
            form: props.form,
            id: props.id,
            name: props.field.name,
            onInput: e => context.emit('update:modelValue', e.target.value),
            value: props.modelValue
        },
        props.field.options.options.map(option => h(AppOption, {key: option.value, option}))
    )
}

AppSelect.emits = ['update:modelValue']
AppSelect.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppSelect
