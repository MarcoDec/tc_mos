import AppInput from './AppInput'
import AppInputFile from './AppInputFile'
import AppSelect from './select/AppSelect'
import {generateField} from '../../../validators'
import {h} from 'vue'

function getType(field) {
    switch (field.type) {
    case 'file':
        return AppInputFile
    case 'select':
        return AppSelect
    default:
        return AppInput
    }
}

function AppInputGuesser(props, context) {
    return h(getType(props.field), {
        disabled: props.disabled,
        field: props.field,
        form: props.form,
        id: props.id,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => context.emit('update:modelValue', value)
    })
}

AppInputGuesser.emits = ['update:modelValue']
AppInputGuesser.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppInputGuesser
