import {h, resolveComponent} from 'vue'
import AppInputFile from './AppInputFile'
import AppInputMeasure from './AppInputMeasure.vue'
import AppInputMeasureSelect from './AppInputMeasureSelect.vue'
import AppInputNumber from './AppInputNumber.vue'
import AppMultiselect from './select/AppMultiselect.vue'
import AppRating from './AppRating.vue'
import AppSelect from './select/AppSelect.vue'
import AppSwitch from './AppSwitch.vue'
import AppTrafficLight from './AppTrafficLight.vue'
import AppTextArea from './AppTextArea'
import {generateField} from '../../../props'

function getType(field) {
    switch (field.type) {
    case 'boolean':
        return AppSwitch
    case 'number':
        return AppInputNumber
    case 'file':
        return AppInputFile
    case 'select':
        return AppSelect
    case 'multiselect':
        return AppMultiselect
    case 'measure':
        return AppInputMeasure
    case 'measureSelect':
        return AppInputMeasureSelect
    case 'rating':
        return AppRating
    case 'textarea':
        return AppTextArea
    default:
        return resolveComponent('AppInputJS')
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
