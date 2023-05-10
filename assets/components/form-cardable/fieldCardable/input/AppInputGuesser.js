import {h, resolveComponent} from 'vue'
import AppGroupButton from './AppGroupButton.vue'
import AppInputFile from './AppInputFile'
import AppInputNumber from './AppInputNumber'
import AppSelect from '../../../form/field/input/select/AppSelect.vue'
import AppSwitch from './AppSwitch.vue'
import {generateField} from '../../../props'

function getType(field) {
    switch (field.type) {
    case 'boolean':
        return AppSwitch
    case 'file':
        return AppInputFile
    case 'number':
        return AppInputNumber
    case 'select':
        return AppSelect
    case 'grpbutton':
        return AppGroupButton
    default:
        return resolveComponent('AppInput')
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
    form: {required: false, type: String},
    id: {required: false, type: String},
    modelValue: {}
}

export default AppInputGuesser
