import {h, resolveComponent} from 'vue'
import AppInputFile from './AppInputFile'
import AppInputNumberJS from './AppInputNumber.js'
import AppMultiselect from './select/AppMultiselect.vue'
import AppSelectJS from './select/AppSelectJS'
import AppSwitch from './AppSwitch.vue'
import {generateField} from '../../../props'

function getType(field) {
    switch (field.type) {
    case 'boolean':
        return AppSwitch
    case 'file':
        return AppInputFile
    case 'number':
        return AppInputNumberJS
    case 'select':
        return AppSelectJS
    case 'multiselect':
        return AppMultiselect
    default:
        return resolveComponent('AppInputJS')
    }
}

function AppInputGuesserJS(props, context) {
    return h(getType(props.field), {
        disabled: props.disabled,
        field: props.field,
        form: props.form,
        id: props.id,
        modelValue: props.modelValue,
        'onUpdate:modelValue': value => context.emit('update:modelValue', value)
    })
}

AppInputGuesserJS.emits = ['update:modelValue']
AppInputGuesserJS.props = {
    disabled: {type: Boolean},
    field: generateField(),
    form: {required: true, type: String},
    id: {required: true, type: String},
    modelValue: {}
}

export default AppInputGuesserJS
